<?php
/**
 * Autogenerated by Thrift
 *
 * DO NOT EDIT UNLESS YOU ARE SURE THAT YOU KNOW WHAT YOU ARE DOING
 *  @generated
 */
require_once $GLOBALS['THRIFT_ROOT'].'/Thrift.php';


final class ResultCode {
  const OK = 0;
  const TRY_LATER = 1;
  static public $__names = array(
    0 => 'OK',
    1 => 'TRY_LATER',
  );
  static public $__values = array(
    'OK' => 0,
    'TRY_LATER' => 1,
  );
}

$GLOBALS['E_ResultCode'] = ResultCode::$__values;

class LogEntry implements IThriftStruct {
  static $_TSPEC = array(
    1 => array(
      'var' => 'category',
      'type' => TType::STRING,
      ),
    2 => array(
      'var' => 'message',
      'type' => TType::STRING,
      ),
    );
  public static $_TFIELDMAP = array(
    'category' => 1,
    'message' => 2,
  );
  const STRUCTURAL_ID = 430918051149326369;
  public $category = null;
  public $message = null;

  public function __construct($vals=null) {
    if (is_array($vals)) {
      if (isset($vals['category'])) {
        $this->category = $vals['category'];
      }
      if (isset($vals['message'])) {
        $this->message = $vals['message'];
      }
    } else if ($vals) {
      throw new TProtocolException(
        'LogEntry constructor must be passed array or null'
      );
    }
  }

  public function getName() {
    return 'LogEntry';
  }

  public static function __set_state($vals) {
    return new LogEntry($vals);
  }

  public function read(TProtocol $input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      if (!$fid && $fname !== null) {
        if (isset(self::$_TFIELDMAP[$fname])) {
          $fid = self::$_TFIELDMAP[$fname];
          $ftype = self::$_TSPEC[$fid]['type'];
        }
      }
      switch ($fid)
      {
        case 1:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->category);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->message);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
      $xfer += $input->readFieldEnd();
    }
    $xfer += $input->readStructEnd();
    return $xfer;
  }

  public function write(TProtocol $output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('LogEntry');
    if ($this->category !== null) {
      $xfer += $output->writeFieldBegin('category', TType::STRING, 1);
      $xfer += $output->writeString($this->category);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->message !== null) {
      $xfer += $output->writeFieldBegin('message', TType::STRING, 2);
      $xfer += $output->writeString($this->message);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}

?>
