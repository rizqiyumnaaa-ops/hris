<?php
/**
 * Base Model Class
 */

require_once dirname(__DIR__) . '/config/database.php';

class BaseModel {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
}
