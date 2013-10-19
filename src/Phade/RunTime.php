<?php

function phade_merge($a, $b) {
    $ac = $a['class'];
  $bc = $b['class'];

  if ($ac || $bc) {
      $ac = $ac || [];
      $bc = $bc || [];
      if (!is_array($ac)) $ac = [$ac];
      if (!is_array($bc)) $bc = [$bc];
      $a['class'] = array_filter(array_merge($ac, $bc), 'strlen');
  }

  foreach($b as $key => $val) {
        if ($key != 'class') {
            $a[$key] = $b[$key];
    }
    }

  return $a;
};

function phade_attrs($obj, $escaped){
    $buf = [];
    $terse = $obj;
    //delete $obj->terse;
    $keys = array_keys(get_object_vars($obj));
  $len = sizeof($keys);

  if ($len) {
      array_push($buf,'');
      for ($i = 0; $i < $len; ++$i) {
          $key = $keys[$i];
        $val = $obj[$key];

      if (is_bool($val) || null == $val) {
              if ($val) {
                  $terse
                      ? array_push($buf, $key)
                      : array_push($buf, $key . '="' . $key . '"');
              }
          } else if (0 === strpos($key,'data') && !is_string($val)) {
              array_push($buf, $key . "='" . preg_replace("/'/", '&apos;', json_encode($val)) . "'");
      } else if ('class' == $key) {
        if ($escaped && $escaped[$key]){
          if ($val = phade_escape(phade_join_classes($val))) {
            array_push($buf, $key . '="' . $val . '"');
          }
        } else {
          if ($val = phade_join_classes($val)) {
            array_push($buf, $key . '="' . $val . '"');
          }
        }
      } else if ($escaped && $escaped[$key]) {
        array_push($buf, $key . '="' . phade_escape($val) . '"');
      } else {
        array_push($buf, $key . '="' . $val . '"');
      }
    }
  }

  return join($buf, ' ');
};

/**
 * @param $html
 * @return string
 */
function phade_escape($html){
    return htmlspecialchars($html);
};

/**
 * join array as classes.
 *
 * @param mixed $val
 * @return string
 * @api private
 */
function phade_join_classes($val) {
    return is_array($val) ? join(array_filter(array_map($val, 'phade_join_classes'), 'strlen'),' ') : $val;
}