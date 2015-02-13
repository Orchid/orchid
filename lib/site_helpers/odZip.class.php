<?php

class odZip {
  public function __construct() {}

  /**
   * Add files and sub-directories in a folder to zip file.
   */
  private static function folderToZip($folder, &$zipFile, $exclusiveLength) {
    $handle = opendir($folder);
    while (false !== $f = readdir($handle)) {
      if ($f != '.' && $f != '..') {
        $filePath = "$folder/$f";
        // Remove prefix from file path before add to zip.
        $localPath = substr($filePath, $exclusiveLength);
        if (is_file($filePath)) {
          $zipFile->addFile($filePath, $localPath);
        } elseif (is_dir($filePath)) {
          // Add sub-directory.
          $zipFile->addEmptyDir($localPath);
          self::folderToZip($filePath, $zipFile, $exclusiveLength);
        }
      }
    }
    closedir($handle);
  }

  /**
   * Zip a folder (include itself).
   * Usage:
   *   odZip::zipDir('/path/to/sourceDir', '/path/to/out.zip');
   */
  public static function zipDir($sourcePath, $outZipPath) {
    $pathInfo = pathInfo($sourcePath);
    $parentPath = $pathInfo['dirname'];
    $dirName = $pathInfo['basename'];

    $z = new ZipArchive();
    $z->open($outZipPath, ZIPARCHIVE::CREATE);
    $z->addEmptyDir($dirName);
    self::folderToZip($sourcePath, $z, strlen($parentPath."/"));
    $z->close();
  }
  
  public static function unZip($source, $destination) {
    @mkdir($destination, 0777, true);

    $zip = new ZipArchive;
    if ($zip->open($source) === true) {
      $zip->extractTo($destination);
      $zip->close();
    }
  }
}

?>