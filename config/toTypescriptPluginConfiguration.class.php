<?php

class toTypescriptPluginConfiguration extends sfPluginConfiguration
{
  public 
    $exe;

  public function configure(){
    $this->dispatcher->connect('dm.layout.filter_javascripts', array($this, 'listenToFilterJavaScriptsEvent'));
  }
  
  public function listenToFilterJavaScriptsEvent(sfEvent $event, array $assets){
    $this->exe = trim(sfConfig::get('app_typescript_executable', ''));
    $vals = array_values($assets);
    $assets = array_combine(array_map(array($this, 'handleJS'), array_keys($assets)), $vals);   
    unset($assets['']);
    return $assets;
  }
  
  public function handleJS($strAssetPath){
    if(false === stripos($strAssetPath, 'ts.js'))
      return $strAssetPath;

    if(empty($this->exe))
      return '';
    
    $assetPath = sfConfig::get('sf_web_dir') . $strAssetPath;
    $tsFile = substr($strAssetPath, 0, -3);
    $tsPath = sfConfig::get('sf_web_dir') . $tsFile;
    
    
    if(!file_exists($tsPath))
      return '';
    
    $mTimeTsFile = filemtime($tsPath);
    
    // caching
    if(file_exists($assetPath) && $mTimeTsFile == filemtime($assetPath)){
      return $strAssetPath;
    }
    
    $tsPath = realpath($tsPath);

    // server-side
    $done = false;
    if(!empty($this->exe)){
      $output = array();
      $retVal = -1;
      $cmd = sprintf('%s %s --out %s --allowbool --allowimportmodule', escapeshellcmd($this->exe), escapeshellarg($tsPath), escapeshellarg($assetPath));
      
      $lastLine = exec($cmd, $output, $retVal);
      $done = $retVal === 0 && file_exists($assetPath);
      if($done){
          touch($assetPath, $mTimeTsFile);
          return $strAssetPath;
      }
      else{
        $msg = (count($output) > 5 ? $lastLine  : implode(PHP_EOL, $output)) . sprintf(' exit_code: %d', $retVal);
        throw new Exception($msg);
      }
    }
    
    // client side not supported
    return '';
  }
}