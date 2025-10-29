<?php
/* ============================================
   CLASE DE BASE DE DATOS - CLÍNICA VETERINARIA ALASKA
   Clase wrapper para MySQLi con métodos útiles
   ============================================ */

class DB
{
    // Declaración de variables
    protected $connection;
    protected $query;
    protected $show_errors = TRUE;
    protected $query_closed = TRUE;
    public $query_count = 0;
    public $insert_id;
    protected $in_transaction = FALSE;

    /**
     * Constructor - Establece la conexión a la base de datos
     * @param string $dbhost Host de la base de datos
     * @param string $dbuser Usuario de la base de datos
     * @param string $dbpass Contraseña de la base de datos
     * @param string $dbname Nombre de la base de datos
     * @param string $charset Charset de la conexión
     */
    public function __construct($dbhost = 'localhost', $dbuser = 'root', $dbpass = '', $dbname = 'clinica_veterinaria', $charset = 'utf8mb4')
    {
        $this->connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

        if ($this->connection->connect_error) {
            $this->error('No se pudo conectar a MySQL - ' . $this->connection->connect_error);
        }

        $this->connection->set_charset($charset);
    }

    /**
     * Ejecuta una consulta SQL preparada
     * @param string $query Consulta SQL
     * @param mixed ...$params Parámetros para bind
     * @return $this
     */
    public function query($query)
    {
        if (!$this->query_closed) {
            $this->query->close();
        }

        if ($this->query = $this->connection->prepare($query)) {
            if (func_num_args() > 1) {
                $x = func_get_args();
                $args = array_slice($x, 1);
                $types = '';
                $args_ref = array();

                foreach ($args as $k => &$arg) {
                    if (is_array($args[$k])) {
                        foreach ($args[$k] as $j => &$a) {
                            $types .= $this->_gettype($args[$k][$j]);
                            $args_ref[] = &$a;
                        }
                    } else {
                        $types .= $this->_gettype($args[$k]);
                        $args_ref[] = &$arg;
                    }
                }

                // Verificar si hay tipos para bind_param
                if (!empty($types) && !empty($args_ref)) {
                    array_unshift($args_ref, $types);
                    call_user_func_array(array($this->query, 'bind_param'), $args_ref);
                }
            }

            $this->query->execute();

            if ($this->query->errno) {
                $this->error('No se puede procesar la consulta de MySQL (revisa tus parámetros) - ' . $this->query->error);
            }

            $this->query_closed = FALSE;
            $this->query_count++;
        } else {
            $this->error('No se puede preparar la declaración de MySQL (revisa tu sintaxis) - ' . $this->connection->error);
        }

        return $this;
    }

    /**
     * Obtiene todos los resultados de la consulta
     * @param callable|null $callback Función callback opcional
     * @return array
     */
    public function fetchAll($callback = null)
    {
        $params = array();
        $row = array();
        $meta = $this->query->result_metadata();

        if (!$meta) {
            // Si no hay metadata, retornar un array vacío
            return [];
        }

        while ($field = $meta->fetch_field()) {
            $params[] = &$row[$field->name];
        }

        call_user_func_array(array($this->query, 'bind_result'), $params);
        $result = array();

        while ($this->query->fetch()) {
            $r = array();
            foreach ($row as $key => $val) {
                $r[$key] = $val;
            }

            if ($callback != null && is_callable($callback)) {
                $value = call_user_func($callback, $r);
                if ($value == 'break') break;
            } else {
                $result[] = $r;
            }
        }

        $this->query->free_result();
        $this->query->close();
        $this->query_closed = TRUE;

        return $result;
    }

    /**
     * Obtiene un único registro como array asociativo
     * @return array
     */
    public function fetchArray()
    {
        $params = array();
        $row = array();
        $meta = $this->query->result_metadata();

        if (!$meta) {
            return [];
        }

        while ($field = $meta->fetch_field()) {
            $params[] = &$row[$field->name];
        }

        call_user_func_array(array($this->query, 'bind_result'), $params);
        $result = array();

        while ($this->query->fetch()) {
            foreach ($row as $key => $val) {
                $result[$key] = $val;
            }
        }

        $this->query->store_result();
        $this->query->close();
        $this->query_closed = TRUE;

        return $result;
    }

    /**
     * Obtiene solo el primer registro
     * @return array|null
     */
    public function fetchOne()
    {
        $results = $this->fetchAll();
        return !empty($results) ? $results[0] : null;
    }

    /**
     * Cierra la conexión a la base de datos
     * @return bool
     */
    public function close()
    {
        return $this->connection->close();
    }

    /**
     * Obtiene el número de filas de la consulta
     * @return int
     */
    public function numRows()
    {
        $this->query->store_result();
        return $this->query->num_rows;
    }

    /**
     * Obtiene el número de filas afectadas
     * @return int
     */
    public function affectedRows()
    {
        return $this->query->affected_rows;
    }

    /**
     * Obtiene el último ID insertado
     * @return int
     */
    public function getInsertId()
    {
        return $this->connection->insert_id;
    }

    /**
     * Muestra el error y termina la ejecución
     * @param string $error Mensaje de error
     */
    public function error($error)
    {
        if ($this->show_errors) {
            // En producción, mejor usar un log
            error_log($error);
            exit($error);
        }
    }

    /**
     * Obtiene el tipo de dato para bind_param
     * @param mixed $var Variable a evaluar
     * @return string
     */
    private function _gettype($var)
    {
        if (is_string($var))
            return 's';
        if (is_float($var))
            return 'd';
        if (is_int($var))
            return 'i';
        return 'b';
    }

    // ============================================
    // MÉTODOS PARA MANEJAR TRANSACCIONES
    // ============================================

    /**
     * Inicia una transacción en la base de datos
     * @return bool
     */
    public function beginTransaction()
    {
        $this->connection->autocommit(FALSE);
        $this->in_transaction = TRUE;
        return $this->connection->begin_transaction();
    }

    /**
     * Confirma una transacción en la base de datos
     * @return bool
     */
    public function commit()
    {
        $result = $this->connection->commit();
        $this->connection->autocommit(TRUE);
        $this->in_transaction = FALSE;
        return $result;
    }

    /**
     * Revierte una transacción en la base de datos
     * @return bool
     */
    public function rollBack()
    {
        $result = $this->connection->rollback();
        $this->connection->autocommit(TRUE);
        $this->in_transaction = FALSE;
        return $result;
    }

    /**
     * Verifica si hay una transacción activa
     * @return bool
     */
    public function inTransaction()
    {
        return $this->in_transaction;
    }

    // ============================================
    // MÉTODOS ADICIONALES ÚTILES
    // ============================================

    /**
     * Escapa una cadena para prevenir SQL Injection
     * @param string $str Cadena a escapar
     * @return string
     */
    public function escape($str)
    {
        return $this->connection->real_escape_string($str);
    }

    /**
     * Verifica si existe al menos un registro
     * @return bool
     */
    public function exists()
    {
        return $this->numRows() > 0;
    }

    /**
     * Obtiene información de la conexión
     * @return array
     */
    public function getConnectionInfo()
    {
        return [
            'host' => $this->connection->host_info,
            'server_version' => $this->connection->server_info,
            'protocol_version' => $this->connection->protocol_version,
            'character_set' => $this->connection->character_set_name()
        ];
    }
}
?>

