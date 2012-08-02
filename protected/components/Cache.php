<?php



/**
 * Простой кеш
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class Cache {
    
    private static $_folder = 'cache';
    
    private static function _getPrefix() {
        return SessionHelper::getSid();
    }
    
    private static function _buildFileName($key) {
        return self::$_folder.'/'.self::_getPrefix().$key.'.dat';
    }
    
    public static function put($key, $data) {
        $fileName = self::_buildFileName($key);
        
        if(!is_dir(self::$_folder)) mkdir(self::$_folder);
        $data = serialize($data);
        file_put_contents($fileName, $data);
    }
    
    public static function get($key) {
        $fileName = self::_buildFileName($key);
        if (!file_exists($fileName)) return null;
        
        $data = file_get_contents($fileName);
        return unserialize($data);
    }
}

?>
