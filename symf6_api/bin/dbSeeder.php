<?php
$totalToSeed = 1000000;
$batchSize = 2000;


if (!$connection = getEnvConnection()){
    exit('Can`t get connection string from env!');
}

if (!$link = reconnectDB()){
    exit('Can`t connect to DB!');
}

$authors = generateFakeAuthors(100);


for ($seedIter = 0; $seedIter < ($totalToSeed / $batchSize); $seedIter++){
    
    insertBatch($link, $connection['dbName'], $authors, $batchSize);
    
    $progress = ($seedIter + 1) * $batchSize / $totalToSeed * 100;

    echo "\r";
    echo "Progress: [" . str_repeat("=", $progress) . str_repeat(" ", 100 - $progress) . "] " . round($progress, 2) . "%";
    // usleep(50000);
}

mysqli_close($link);




function insertBatch($link, $shema, $Authors, $batchSize)
{
    global $link;

    $quryInsertBooks = "INSERT INTO `" . $shema . "`.`book` (`title`, `author`, `description`, `price`) VALUES ";

    for ($batchIter = 0 ; $batchIter < $batchSize; $batchIter++)
    {
        $quryInsertBooks .= "('" . "Book title " . generateFakeSentence() . "', '" . $Authors[rand(0, count($Authors) - 1)] . "', '" . generateFakeDescription() . "', " . rand(1,100) . "." . rand(0,99) . "), ";
    }
    
    $quryInsertBooks = substr($quryInsertBooks, 0, -2);
    
    $link = reconnectDB();
    mysqli_query($link, $quryInsertBooks);

    return mysqli_affected_rows($link);
}



function generateFakeAuthors($limit)
{
    $fakeAuthors = [];

    for ($i = 1; $i <= $limit; $i++) {
        $fakeAuthors[] = "Author " . $i;
    }

    return $fakeAuthors;
}

function generateFakeSentence()
{
    $words = ['Lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur', 'adipiscing', 'elit'];
    $sentence = implode(' ', array_slice($words, 0, rand(5, 10))) . '.';

    return ucfirst($sentence);
}

function generateFakeDescription()
{
    $paragraphs = [];

    for ($i = 1; $i <= rand(3, 5); $i++) {
        $paragraphs[] = "Paragraph " . $i . ": " . generateFakeSentence();
    }

    return implode("\n", $paragraphs);
}

function getEnvConnection()
{
    $envFilePath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '.env';
    $envData = file_get_contents($envFilePath);
    $envLines = explode("\n", $envData);
    
    
    foreach($envLines as $envLine){
        $envLine = trim($envLine);
        if (empty($envLine) || strpos($envLine, '#') === 0) {
            continue;
        }

        list($key, $value) = explode('=', $envLine, 2);
   
        if($key === 'DATABASE_URL')
        {
            $value = str_replace('"', '', $value);
            $parseUrl = parse_url($value);
            return 
            [
                'dbHost' => $parseUrl['host'],
                'dbPort' => $parseUrl['port'],
                'dbUserName' => $parseUrl['user'],
                'dbPassword' => $parseUrl['pass'],
                'dbName' =>  ltrim($parseUrl['path'], '/'),
            ];
        }
    }

    return false;
}

function reconnectDB()
{
    global $link, $connection;
    // if (!$link)
    // {
        $link = mysqli_connect(
            $connection['dbHost'],
            $connection['dbUserName'],
            $connection['dbPassword'],
            $connection['dbName'],
            $connection['dbPort']
        );
    // }

    return $link;
}