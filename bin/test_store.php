<?php
/**
 * This file is part of the proophsoftware/postgres-document-store.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Prooph\EventMachine\Persistence\DocumentStore\Filter;

require_once __DIR__ .'/../vendor/autoload.php';

$dsn = getenv('PDO_DSN');
$usr = getenv('PDO_USER');
$pwd = getenv('PDO_PWD');

$pdo = new \PDO($dsn, $usr, $pwd);
$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

$pdo->prepare("DROP TABLE IF EXISTS em_ds_docs")->execute();
$pdo->prepare("DROP TABLE IF EXISTS em_ds_test")->execute();

$documentStore = new \Prooph\EventMachine\Postgres\PostgresDocumentStore($pdo);

$documentStore->addCollection('test');
$documentStore->addCollection('docs');

$collections = $documentStore->listCollections();

foreach ($collections as $col) {
    echo "Collection: $col\n";
}

$collections = $documentStore->filterCollectionsByPrefix('do');

foreach ($collections as $col) {
    echo "Filtered Collection: $col\n";
}

echo "Has test: " . ($documentStore->hasCollection('test')? 'YES' : 'NO') . "\n";
echo "Has dogs: " . ($documentStore->hasCollection('dogs')? 'YES' : 'NO') . "\n";

$documentStore->dropCollection('test');
echo "Table test dropped\n";
echo "Has test: " . ($documentStore->hasCollection('test')? 'YES' : 'NO') . "\n";

$docId1 = '0e2c39b1-0778-4e92-a06b-0fa1c6ae9c5d';
$docId2 = '0e2ec294-df90-4bd7-a934-74747752f0bb';
$docId3 = '5bd4fdee-bfe2-4e1d-a4bf-b27e57fa1686';

$documentStore->addDoc('docs', $docId1, [
    'animal' => 'dog',
    'name' => 'Jack',
    'age' => 5,
    'character' => [
        'friendly' => 10,
        'wild' => 6,
        'docile' => 8
    ]
]);

$jack = $documentStore->getDoc('docs', $docId1);

echo "Jack: " . json_encode($jack) . "\n";

$documentStore->upsertDoc('docs', $docId2, [
    'animal' => 'cat',
    'name' => 'Tiger',
    'age' => 5,
    'character' => [
        'friendly' => 3,
        'wild' => 7,
        'docile' => 2
    ]
]);

$documentStore->upsertDoc('docs', $docId2, [
    'age' => 3
]);

$tiger = $documentStore->getDoc('docs', $docId2);

echo "Tiger: " . json_encode($tiger) . "\n";

$documentStore->addDoc('docs', $docId3, [
    'animal' => 'cat',
    'name' => 'Gini',
    'age' => 5,
    'character' => [
        'friendly' => 8,
        'wild' => 3,
        'docile' => 4
    ]
]);

$cats = $documentStore->filterDocs('docs', new Filter\EqFilter('animal', 'cat'));

foreach ($cats as $cat) {
    echo "Cat: " . json_encode($cat) . "\n";
}

$documentStore->updateMany('docs', new Filter\GteFilter('character.friendly', 5), ['pet' => true]);

$pets = $documentStore->filterDocs('docs', new Filter\EqFilter('pet', true));

foreach ($pets as $pet) {
    echo "Pet: " . json_encode($pet) . "\n";
}

$superFriendlyPets = $documentStore->filterDocs(
    'docs',
    new Filter\AndFilter(
        new Filter\EqFilter('pet', true),
        new Filter\EqFilter('character.friendly', 10)
    )
);

foreach ($superFriendlyPets as $pet) {
    echo "Super friendly pet: " . json_encode($pet) . "\n";
}

