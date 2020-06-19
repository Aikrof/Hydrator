<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator\Core
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Core;

use Aikrof\Hydrator\Exceptions\HydratorExeption;
use \phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use Exception;
use ReflectionProperty;

/**
 * Class Annotation
 */
class Annotation extends TypeEnum
{
    /** @var self */
    private static $annotation;

    /** @var string */
    private $type;

    /** @var bool */
    private $isIncludeNullable;

    /** @var string */
    private $className;

    /** @var bool */
    private $ignore;

    /**
     * @param array $annotation
     *
     * @return static
     *
     * @throws \Aikrof\Hydrator\Exceptions\HydratorExeption
     */
    protected static function initAnnotation(array $annotation): self
    {
        // If annotation parameter is not set, we will use default
        $type = $annotation['type'] ?? null;
        $isIncludeNullable = $annotation['isIncludeNullable'] ?? false;
        $className = $annotation['className'] ?? '';
        $ignore = $annotation['ignore'] ?? false;

        if (!self::$annotation) {
            self::$annotation = new self($type, $isIncludeNullable, $className, $ignore);
        }
        else {

            self::$annotation->__construct($type, $isIncludeNullable, $className, $ignore);
        }

        return self::$annotation;
    }

    /**
     * @param \phpDocumentor\Reflection\DocBlockFactory $factory
     * @param \ReflectionProperty                       $property
     *
     * @return static|null
     */
    public static function getDoc(DocBlockFactory $factory,  ReflectionProperty $property): ?self
    {
        try {
            $docBlock = $factory->create($property->getDocComment());
        } catch (Exception $e) {
            return null;
        }

        // If we have tag `@internal`, we will ignore annotation of this field
        if ($docBlock->hasTag('internal')) {
            return null;
        }

        // If we have tag `@ignore` field will be ignored
        if ($docBlock->hasTag('ignore')) {
           return  self::initAnnotation(['ignore' => true]);
        }

        if ($docBlock->hasTag('var')) {
            $block = \current($docBlock->getTagsByName('var'));
            $type = $block->getType();

            $stringType = (string)$type;
            $annotation = [
                'type' => $stringType,
                'isIncludeNullable' => false,
            ];

            $getType = false;
            if ($type instanceof Compound) {
                foreach ($type as $data) {
                    if ($data instanceof Null_) {
                         $annotation['isIncludeNullable'] = true;
                        continue;
                     }

                     if (!$getType) {
                         $stringType = (string)$data;
                         if (\in_array($stringType, self::SCALAR_TYPES)) {
                             $annotation['type'] = $stringType;
                         }
                         $type = $data;
                         $getType = true;
                         continue;
                     }

                     throw new HydratorExeption(
                         "{$property->class} - property `{$property->getName()}` must be of one data-type (including nullable). Current value is incorrect: `{$data}`"
                     );
                }
            }

            if ($type instanceof Object_) {
                $annotation['type'] = self::TYPE_OBJECT;
            }
            else if ($type instanceof Array_ && $fq = $type->getValueType()) {
                if ($fq instanceof Object_){
                    $annotation['type'] = self::ARRAY_OF_OBJECT;
                }
                else if ($fq instanceof String_) {
                    $annotation['type'] = self::ARRAY_OF_STRING;
                }
                else if ($fq instanceof Integer) {
                    $annotation['type'] = self::ARRAY_OF_INT;
                }
                else if ($fq instanceof Float_) {
                    $annotation['type'] = self::ARRAY_OF_FLOAT;
                }
            }

            $annotation['className'] = \in_array($annotation['type'], [self::TYPE_OBJECT, self::ARRAY_OF_OBJECT], true)
                ? self::getClassNamespace($stringType, $property->class)
                : '';

            return self::initAnnotation($annotation);
        }

        return null;
    }

    /**
     * @param string $name
     * @param string $path
     *
     * @return string
     */
    protected static function getClassNamespace(string $name, string $path): string
    {
        $name = explode('[', $name)[0];
        $className = $name[0] === '\\' ? \substr($name, 1) : $name;

        $namespace = $className;
        if (!\substr_count($namespace, '\\')) {
            $default = \explode('\\', $path);
            unset($default[\count($default) - 1]);
            $default = \implode('\\', $default);
            $namespace = $default . '\\' . $namespace;
        }

        return $namespace;
    }

    /**
     * Annotation constructor.
     *
     * @param string|null   $type
     * @param bool          $isIncludeNullable
     * @param string        $className
     * @param bool          $ignore
     */
    public function __construct(?string $type, bool $isIncludeNullable, string $className, bool $ignore)
    {
        $this->type = $type;
        $this->isIncludeNullable = $isIncludeNullable;
        $this->className = $className;
        $this->ignore = $ignore;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isIncludeNullable(): bool
    {
        return $this->isIncludeNullable;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return bool
     */
    public function isIgnore(): bool
    {
        return $this->ignore;
    }

    /**
     * @return bool
     */
    public function isClass(): bool
    {
        return (bool)$this->className;
    }
}