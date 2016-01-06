<?php

class qSandbox_Result {
    public $msg = '';
    public $status = 0;
    public $data = array();

    public function isSuccess() {
        return ! empty( $this->status );
    }

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

    public function data($records = null) {
        if ( ! empty( $value ) ) {
            $this->data[ $key_or_records ] = $value;
        } elseif ( ! is_null( $records ) ) {
            $this->data = $records;
        }

        return $this->data;
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
