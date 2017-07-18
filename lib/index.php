<?php
declare(strict_types=1);
namespace which;

use Rx\{Observable};

/**
 * Created by IntelliJ IDEA.
 * User: Cedric
 * Date: 18/07/2017
 * Time: 13:27
 */
function which(string $command, bool $all = false): Observable {
  return (new Finder)->resolvePath($command, $all);
}
