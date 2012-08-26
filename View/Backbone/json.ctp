<?php

/*
 * The file that renders json for output,
 * when an error is present it outputs it in 
 * the json should it need to be caught.
 */

if ($this->Session->check('Message.error')): 
  '{"error":{"text":'. $this->Session->flash('error') .'}}';
else:
  if (isset($object)):
    echo json_encode($object);
  endif;
  $this->Session->flash();
endif;

?>