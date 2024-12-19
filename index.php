<?php

# Fejlesztői cucc, hibajelentés be/ki ----------------------------------------------------
error_reporting(E_ALL);
ini_set("display_errors", 1);
# Fejlesztői cucc, hibajelentés be/ki ----------------------------------------------------

require_once "vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

function showPicture($key)
{
    global $objAwsS3Client;
    $result = $objAwsS3Client->getObject([
        'Bucket' => $_ENV['AWS_BUCKET_NAME'],
        'Key'    => $key
    ]);
    
    echo "<p><img src='".$result['Body']."'/></p>";
}

use Aws\S3\S3Client;
$objAwsS3Client = new S3Client([
    'version' => 'latest',
    'region' => $_ENV['AWS_ACCESS_REGION'],
    'credentials' => [
        'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
        'secret' => $_ENV['AWS_ACCESS_KEY_SECRET']
    ]
]);


try {
    $objects = $objAwsS3Client->listObjects(['Bucket' => $_ENV['AWS_BUCKET_NAME']]);
    // loop through all files
    foreach ($objects['Contents'] as $object)
    {
        showPicture($object['Key']);
    }
} catch (Aws\S3\Exception\S3Exception $e) {
    echo "There was an error fetching data from S3: " . $e->getMessage();
}

?>
