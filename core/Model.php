<?php
class Model {
    protected $db;
    protected $table;
    protected $primaryKey;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findAll($orderBy = 'id DESC') {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*(?:\s+(?:ASC|DESC))?$/i', $orderBy)) {
            $orderBy = $this->primaryKey . ' DESC';
        }
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function find($conditions = [], $orderBy = 'id DESC') {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $key => $value) {
                $where[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*(?:\s+(?:ASC|DESC))?$/i', $orderBy)) {
            $orderBy = $this->primaryKey . ' DESC';
        }
        $sql .= " ORDER BY {$orderBy}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "{$key} = :{$key}";
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . " WHERE {$this->primaryKey} = :id";
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $key => $value) {
                $where[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'];
    }

    public function search($column, $term) {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column)) {
            return [];
        }
        $sql = "SELECT * FROM {$this->table} WHERE {$column} LIKE :term ORDER BY {$this->primaryKey} DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['term' => "%{$term}%"]);
        return $stmt->fetchAll();
    }
}
