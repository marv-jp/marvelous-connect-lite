<?php


class Common_Db_DataSourceManager
{
    
    /**
     * @var array
     */
    protected $_datasources = array();

    /**
     * @var array
     */
    protected $_config = array();

    /**
     * @var Zend_Cache_Core
     */
    protected $_cache;

    /**
     * @var string
     */
    protected $_cacheTag;

    /**
     * @var string
     */
    protected $_cacheKeyTag = 'datasource_manager_tag_%s';
    
    /**
     * @var string
     */    
    protected $_cacheKeyTableSection = 'datasource_manager_table_section';

    /**
     * @var array
     */    
    protected $_tableSection;

    /**
     * コンストラクタ。
     *
     * @param array|Zend_Config $config
     * @param Zend_Cache_Core $cache
     * @param string $cacheTag
     */
    public function __construct($config, Zend_Cache_Core $cache, $cacheTag)
    {
        $this->_config = $config;
        $this->_cache = $cache;
        $this->_cacheTag = $cacheTag;
        foreach ($config as $section => $dbConfig) {
            $this->_datasources[$section] = new Common_Db_DataSource($dbConfig, $cache, $cacheTag.$section);
        }
    }
    
    /**
     * DBアダプターを返す。
     * 
     * @param string $section
     * @param string $role
     * @return Zend_Db_Adapter_Abstract
     */
    public function getAdapter($section, $role)
    {
        return $this->_datasources[$section]->getAdapter($role, $section);
    }

    /**
     * 全DataSourceインスタンスを返す。
     * 
     * @return array
     */
    public function getDataSources()
    {
        return $this->_datasources;
    }
    
    /**
     * DBセクション名の配列を返す。
     * 
     * @return array
     */
    public function getDbSections()
    {
        return array_keys($this->_datasources);
    }

    /**
     * タグ名をもとに対応するDBセクション名を返す
     * 
     * @param array|string $tag
     * @return array
     * @throws Zend_Db_Exception
     */
    public function getDbSectionsByTag($tag)
    {
        if (!is_array($tag)) {
            $tag = array($tag);
        }
        
        $cacheKey = sprintf($this->_cacheKeyTag, sha1(serialize($tag)));
        $dbSections = $this->_cache->load($cacheKey);
        if ($dbSections) {
            return $dbSections;
        }
        foreach ($this->_config as $dbSection => $config) {
            if (array_intersect($tag, $config['tags'])) {
                $dbSections[] = $dbSection;
            }
        }
        if (empty($dbSections)) {
            throw new Zend_Db_Exception('Tag config dose not exist.');
        }
        $this->_cache->save($dbSections, $cacheKey, array($this->_cacheTag));
        return $dbSections;
    }

    /**
     * テーブル名をもとに対応するDBセクション名を返す
     * 
     * @param string $table テーブル名
     * @return string DBセクション名
     */
    public function getDbSectionByTable($table)
    {
        $key = Common_Util_String::camelToSnake($table);
        if ($this->_tableSection) {
            return $this->_tableSection[$key];
        }
        $cachedTableSection = $this->_cache->load($this->_cacheKeyTableSection);
        if ($cachedTableSection) {
            $this->_tableSection = $cachedTableSection;
            return $this->_tableSection[$key];
        }
        $this->_tableSection = $this->_createTableSectionMap($this->_config);
        $this->_cache->save($this->_tableSection, $this->_cacheKeyTableSection, array($this->_cacheTag));
        return $this->_tableSection[$key];
    }
    
    protected function _createTableSectionMap($configs)
    {
        $tableSectionMap = array();
        foreach ($configs as $dbSection => $config) {
            if (!isset($config['tables'])) {
                continue;
            }
            foreach ($config['tables'] as $table) {
                $tableSectionMap[$table] = $dbSection;
            }
        }
        return $tableSectionMap;
    }
    
    
    /**
     * コネクション削除
     */
    public function closeAllConnection()
    {
        foreach ($this->getDataSources() as $section => $dataSource) {
            $adapter  = $dataSource->getAdapter(Common_Db_DataSource::SUPPLIER_SERVER, $section);
            $adapter->closeConnection();
        }
    }

}
