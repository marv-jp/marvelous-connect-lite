<?php

/**
 * OpenSocialのData Collection
 * 
 * http://opensocial.github.io/spec/2.5.1/Core-API-Server.xml#Collection-Request-Parameters
 */
class Misp_Collection_OpenSocial_Collection extends SplObjectStorage
{
    const COUNT          = 'count';
    const START_INDEX    = 'startIndex';
    const FILTER_BY      = 'filterBy';
    const FILTER_OP      = 'filterOp';
    const FILTER_VALUE   = 'filterValue';
    const FIELDS         = 'fields';
    const TOTAL_RESULTS  = 'totalResults';
    const ITEMS_PER_PAGE = 'itemPerPage';

    /** @var int count指定のボーダー */
    const COUNT_BORDER = 1;

    /** @var int デフォルト値：count */
    const DEFAULT_COUNT = 10;

    /** @var int デフォルト値：startIndex */
    const DEFAULT_START_INDEX = 1;

    /** @var string contains */
    const FILTER_OP_VALUE_CONTAINS = 'contains';

    /** @var string equals */
    const FILTER_OP_VALUE_EQUALS = 'equals';

    /** @var string startsWith */
    const FILTER_OP_VALUE_STARTSWITH = 'startsWith';

    /** @var string present */
    const FILTER_OP_VALUE_PRESENT = 'present';

    /** @var string ascending */
    const SORT_ORDER_ASCENDING = 'ascending';

    /** @var string descending */
    const SORT_ORDER_DESCENDING = 'descending';

    /** @var int count */
    protected $_count;

    /** @var string Filter By */
    protected $_filterBy;

    /** @var string Filter Operation */
    protected $_filterOp;

    /** @var string Filter Value */
    protected $_filterValue;

    /** @var string Sort Order */
    protected $_sortOrder;

    /** @var int Start Index */
    protected $_startIndex = 1;

    /** @var int Items Per Page */
    protected $_itemsPerPage = 1;

    /** @var int totalResults */
    protected $_totalResults = 1;

    /** @var string fields */
    protected $_fields;

    public function __construct($options = NULL)
    {
        // Zendリクエストオブジェクトを元ネタに構築する
        if ($options instanceof Zend_Controller_Request_Abstract) {

            $this->_count       = $options->getParam('count', self::DEFAULT_COUNT);
            $this->_fields      = $options->getParam('fields');
            $this->_filterBy    = $options->getParam('filterBy');
            $this->_filterOp    = $options->getParam('filterOp');
            $this->_filterValue = $options->getParam('filterValue');
            $this->_sortOrder   = $options->getParam('sortOrder');
            $this->_startIndex  = $options->getParam('startIndex', self::DEFAULT_START_INDEX);
            //
        } elseif (is_array($options)) {

            // 連想配列を元ネタにざっくり構築する
            $methods = get_class_methods($this);

            foreach ($options as $key => $value) {
                // 正規表現でスネークケース方式から、キャメルケース方式に名前を変換
                $method = 'set' . ucfirst(preg_replace('/_(.)/e', 'strtoupper(\'\1\')', $key));
                if (in_array($method, $methods)) {
                    $this->$method($value);
                }
            }
        }
    }

    /**
     * 
     * @param int $count
     */
    public function setCount($count)
    {
        $this->_count = $count;
    }

    /**
     * 
     * @return int
     */
    public function getCount()
    {
        return $this->_count;
    }

    /**
     * 
     * @param string $filterBy
     */
    public function setFilterBy($filterBy)
    {
        $this->_filterBy = $filterBy;
    }

    /**
     * 
     * @return string
     */
    public function getFilterBy()
    {
        return $this->_filterBy;
    }

    /**
     * 
     * @param string $filterOp
     */
    public function setFilterOp($filterOp)
    {
        $this->_filterOp = $filterOp;
    }

    /**
     * 
     * @return string
     */
    public function getFilterOp()
    {
        return $this->_filterOp;
    }

    /**
     * 
     * @param string $filterValue
     */
    public function setFilterValue($filterValue)
    {
        $this->_filterValue = $filterValue;
    }

    /**
     * 
     * @return string
     */
    public function getFilterValue()
    {
        return $this->_filterValue;
    }

    /**
     * 
     * @param string $sortOrder
     */
    public function setSortOrder($sortOrder)
    {
        $this->_sortOrder = $sortOrder;
    }

    /**
     * 
     * @return string
     */
    public function getSortOrder()
    {
        return $this->_sortOrder;
    }

    /**
     * 
     * @param int $startIndex
     */
    public function setStartIndex($startIndex)
    {
        $this->_startIndex = $startIndex;
    }

    /**
     * 
     * @return int
     */
    public function getStartIndex()
    {
        return $this->_startIndex;
    }

    /**
     * 
     * @param int $itemsPerPage
     */
    public function setItemsPerPage($itemsPerPage)
    {
        $this->_itemsPerPage = $itemsPerPage;
    }

    /**
     * 
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->_itemsPerPage;
    }

    /**
     * 
     * @param int $totalResults
     */
    public function setTotalResults($totalResults)
    {
        $this->_totalResults = $totalResults;
    }

    /**
     * 
     * @return int
     */
    public function getTotalResults()
    {
        return $this->_totalResults;
    }

    /**
     * 
     * @param string $fields
     */
    public function setFields($fields)
    {
        $this->_fields = $fields;
    }

    /**
     * 
     * @return string
     */
    public function getFields()
    {
        return $this->_fields;
    }

}
