<?php
require_once("inc/Store.php");
print(json_encode(Store::read_all()));
