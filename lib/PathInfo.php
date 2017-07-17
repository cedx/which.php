<?php
declare(strict_types=1);
namespace which;

/**
 * Class PathInfo
 * @package which
 */
class PathInfo {

  /**
   * @var array
   */
  private $env;

  /**
   * @var array
   */
  private $ext;

  /**
   * @var string
   */
  private $extExe;

  /**
   * Initializes a new instance of the class.
   * @param array $env
   * @param array $ext
   * @param string $extExe
   */
  public function __construct(array $env = [''], array $ext = [''], string $extExe = '') {
    $this->setEnv($env);
    $this->setExt($ext);
    $this->setExtExe($extExe);
  }

  /**
   * Returns a string representation of this object.
   * @return string The string representation of this object.
   */
  public function __toString(): string {
    $json = json_encode($this, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    return static::class." $json";
  }

  /**
   * Gets
   * @return array
   */
  public function getEnv(): array {
    return $this->env;
  }

  /**
   * Gets
   * @return array
   */
  public function getExt(): array {
    return $this->ext;
  }

  /**
   * Gets
   * @return string
   */
  public function getExtExe(): string {
    return $this->extExe;
  }

  /**
   * Sets
   * @param array $value
   * @return PathInfo
   */
  public function setEnv(array $value): self {
    $this->env = $value;
    return $this;
  }

  /**
   * Sets
   * @param array $value
   * @return PathInfo
   */
  public function setExt(array $value): self {
    $this->ext = $value;
    return $this;
  }

  /**
   * Sets
   * @param string $value
   * @return PathInfo
   */
  public function setExtExe(string $value): self {
    $this->extExe = $value;
    return $this;
  }
}
