<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Pokemon;
use App\Entity\PokemonTranslation;
use App\Entity\PokemonType;
use App\Entity\PokemonTypeAffinity;
use App\Enum\PokemonGrowthRateEnum;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use LogicException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class LoadCsvToDB extends Command
{
    protected static $defaultName = 'app:load-csv';

    private const BATCH_SIZE = 50;

    private EntityManagerInterface $em;
    private string                 $projectDir;

    /**
     * @var array<string, array<int, array{affinity: string, type: string}>>
     */
    private array $typeAffinities = [];

    public function __construct(EntityManagerInterface $em, string $projectDir)
    {
        parent::__construct();
        $this->em         = $em;
        $this->projectDir = $projectDir;
    }

    protected function configure(): void
    {
        $this->setDescription('Load CSV to Database');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->csvHandlers() as $csvHandler) {
            $path   = $this->projectDir.'/'.$csvHandler['csv'];
            $clears = $csvHandler['clear'] ?? [];
            if (!file_exists($path)) {
                $output->writeln("<error>Failed to read file '$path'.</error>");

                return Command::FAILURE;
            }
            $output->writeln("Loading $path.");

            $fp = fopen($path, 'r');
            if (!$fp) {
                $output->writeln("<error>Failed to open file '$path'.</error>");

                return Command::FAILURE;
            }
            $csv = fgetcsv($fp);
            if (!$csv) {
                $output->writeln("<error>Failed to parse CSV '$path'.</error>");

                return Command::FAILURE;
            }
            $headers = array_flip($csv);
            for ($i = 1; $row = fgetcsv($fp); $i++) {
                $entity = $csvHandler['handler']($headers, $row, $i);
                if (null === $entity) {
                    continue;
                }
                $this->em->persist($entity);

                if (($i % self::BATCH_SIZE) === 0) {
                    $this->em->flush();
                    foreach ($clears as $clear) {
                        $this->em->clear($clear);
                    }
                }
            }

            $this->em->flush();
            foreach ($clears as $clear) {
                $this->em->clear($clear);
            }

            $onCompletion = $csvHandler['onCompletion'] ?? null;
            if ($onCompletion) {
                $onCompletion();
            }

            fclose($fp);
            $output->writeln("$i items loaded.");
        }

        return Command::SUCCESS;
    }

    /**
     * List of handlers for each CSV files to populate the database.
     * Must define 'csv' and 'handler' key.
     * Clear is optional and list which entities can be detached during bulk inserts.
     *
     * @return array<int, array{csv: string, handler: callable, onCompletion?: callable, clear?: array<int, string>}>
     */
    private function csvHandlers(): array
    {
        return [
            [
                'csv'          => 'assets/types.csv',
                'handler'      => fn($h, $r, $n) => $this->rowToType($h, $r, $n),
                'onCompletion' => fn() => $this->saveTypeAffinities(),
            ],
            [
                'csv'     => 'assets/pokedex.csv',
                'handler' => fn($h, $r, $n) => $this->rowToPokemon($h, $r),
                'clear'   => [Pokemon::class, PokemonTranslation::class],
            ],
        ];
    }

    /**
     * @param array<string, int> $headers
     * @param array<int, string> $row
     *
     * @return Pokemon|null
     */
    private function rowToPokemon(array $headers, array $row): ?Pokemon
    {
        static $lastInsertedPokemonNumber = -1;

        $number = intval($row[$headers['pokedex_number']]);
        if ($number === $lastInsertedPokemonNumber) {
            return null;
        }

        $lastInsertedPokemonNumber = $number;

        $data = [
            'id'             => $number,
            'translations'   => self::getTranslations($headers, $row, 'name'),
            'type1'          => $this->getTypeFromName($row[$headers['type_1']]),
            'type2'          => $this->getTypeFromName($row[$headers['type_2']]),
            'height'         => floatval($row[$headers['height_m']]),
            'weight'         => floatval($row[$headers['weight_kg']]),
            'hp'             => intval($row[$headers['hp']]),
            'attack'         => intval($row[$headers['attack']]),
            'defense'        => intval($row[$headers['defense']]),
            'spAttack'       => intval($row[$headers['sp_attack']]),
            'spDefense'      => intval($row[$headers['sp_defense']]),
            'speed'          => intval($row[$headers['speed']]),
            'catchRate'      => intval($row[$headers['catch_rate']]),
            'baseExperience' => intval($row[$headers['base_experience']]),
            'baseFriendship' => intval($row[$headers['base_friendship']]),
            'growthRate'     => PokemonGrowthRateEnum::tryFrom(
                str_replace(' ', '_', strtolower($row[$headers['growth_rate']]))
            ),
            'percentageMale' => floatval($row[$headers['percentage_male']]),
            'eggCycles'      => intval($row[$headers['egg_cycles']]),
        ];

        if (!$data['type1']) {
            throw new LogicException('Could not read data type_1 from csv.');
        }
        if (!$data['growthRate']) {
            throw new LogicException('Could not read data grow_rate from csv.');
        }

        return new Pokemon(...$data);
    }

    /**
     * @param array<string, int> $headers
     * @param array<int, string> $row
     * @param int                $n
     *
     * @return PokemonType
     */
    private function rowToType(array $headers, array $row, int $n): PokemonType
    {
        $data = [
            'id'           => $n,
            'translations' => self::getTranslations($headers, $row, 'name'),
        ];

        $affinities = array_slice(array_flip($headers), 3, null, true);

        $this->typeAffinities[$row[0]] = array_map(
            fn($v, $k) => ['type' => $v, 'affinity' => $row[$k]],
            $affinities,
            array_keys($affinities)
        );

        return new PokemonType(...$data);
    }

    /**
     * @return array<string, PokemonType>
     */
    private function getTypes(): array
    {
        static $types = [];

        if (empty($types)) {
            foreach ($this->em->getRepository(PokemonType::class)->findAll() as $type) {
                $types[$type->getName()] = $type;
            }
        }

        return $types;
    }

    private function getTypeFromName(string $name): ?PokemonType
    {
        if (!$name) {
            return null;
        }

        return $this->getTypes()[$name];
    }

    private function saveTypeAffinities(): void
    {
        $types = $this->getTypes();

        foreach ($types as $name => $type) {
            foreach ($this->typeAffinities[$name] as $typeAffinity) {
                $affinity = floatval($typeAffinity['affinity']);
                if (1. === $affinity) {
                    continue;
                }
                $typeAffinity = new PokemonTypeAffinity($type, $types[$typeAffinity['type']], $affinity);

                $this->em->persist($typeAffinity);
            }
        }
        $this->em->flush();
    }

    /**
     * @param array<string, int> $headers
     * @param array<int, string> $row
     * @param string             $name
     *
     * @return array<string, array{locale: string, name: string}>
     */
    #[ArrayShape([
        'en' => "array",
        'jp' => "array",
        'fr' => "array",
    ])]
    private static function getTranslations(array $headers, array $row, string $name): array
    {
        return [
            'en' => [
                'name'   => $row[$headers[$name]],
                'locale' => 'en',
            ],
            'jp' => [
                'name'   => $row[$headers["japanese_$name"]],
                'locale' => 'jp',
            ],
            'fr' => [
                'name'   => $row[$headers["french_$name"]],
                'locale' => 'fr',
            ],
        ];
    }
}
