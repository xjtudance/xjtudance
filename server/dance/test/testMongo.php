<?php
$manager = new MongoDB\Driver\Manager("mongodb://127.0.0.1:27017");
$bulk = new MongoDB\Driver\BulkWrite;
$bulk->insert(['x' => 1, 'class'=>'toefl', 'num' => '18']);
$bulk->insert(['x' => 2, 'class'=>'ielts', 'num' => '26']);
$bulk->insert(['x' => 3, 'class'=>'sat', 'num' => '35']);
$manager->executeBulkWrite('test.log', $bulk);
$filter = ['x' => ['$gt' => 1]];
$options = [
    'projection' => ['_id' => 0],
    'sort' => ['x' => -1],
];
$query = new MongoDB\Driver\Query($filter, $options);
$cursor = $manager->executeQuery('test.log', $query);
foreach ($cursor as $document) {
    print_r($document);
}
?>
