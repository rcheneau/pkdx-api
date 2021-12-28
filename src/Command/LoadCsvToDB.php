<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Pokemon;
use App\Entity\PokemonTranslation;
use App\Entity\PokemonType;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class LoadCsvToDB extends Command
{
    protected static $defaultName = 'app:load-csv';

    private const BATCH_SIZE = 50;

    private EntityManagerInterface $em;
    private string                 $projectDir;

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
     * @return array<int, array{csv: string, handler: callable, clear?: array<int, string>}>
     */
    private function csvHandlers(): array
    {
        return [
            [
                'csv'     => 'assets/types.csv',
                'handler' => fn($h, $r, $n) => $this->rowToType($h, $r, $n),
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

        $type1 = $row[$headers['type_1']];
        $type2 = $row[$headers['type_2']];
        $data  = [
            'id'           => $number,
            'translations' => self::getTranslations($headers, $row, 'name'),
            'type1'        => $this->getTypes($type1),
            'type2'        => $type2 !== '' ? $this->getTypes($type2) : null,
        ];

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

        return new PokemonType(...$data);
    }

    private function getTypes(string $name): PokemonType
    {
        static $types = [];

        if (empty($types)) {
            foreach ($this->em->getRepository(PokemonType::class)->findAll() as $type) {
                $types[$type->getName()] = $type;
            }
        }

        return $types[$name];
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
