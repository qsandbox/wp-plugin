<?php

class qSandbox_Result {
    public $msg = '';
    public $status = 0;
    public $data = array();

    /**
     * Cool method which is nicer than checking for a status value.
     * @return bool
     */
    public function isSuccess() {
        return ! empty( $this->status );
    }

    /**
     * Cool method which is nicer than checking for a status value.
     * @return bool
     */
    public function isError() {
        return ! $this->isSuccess();
    }

    /**
     * Getter/setting for status
     * @param bool $status
     * @return bool
     */
    public function status($status = null) {
        if (!is_null($status)) {
            $this->status = $status;
        }

        return $this->status;
    }

    public function msg($msg = null) {
        if (!is_null($msg)) {
            $this->msg = $msg;
        }

        return $this->msg;
    }

    public function data($key = '', $val = null) {
        if (is_array($key)) { // when we pass an array -> override all
            $this->data = empty($this->data) ? $key : array_merge($this->data, $key);
        } elseif (!empty($key)) {
            if (!is_null($val)) { // add/update a value
                $this->data[$key] = $val;
            }

            return isset($this->data[$key]) ? $this->data[$key] : null;
        } else { // nothing return all data
            $val = $this->data;
        }

        return $val;
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name) {
        return isset($this->$name) ? $this->$name : null;
    }

    public function __call($name, $arguments) {
        
    }
}
