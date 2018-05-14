<?php

namespace Charcoal\Cms;

// From 'charcoal-object'
use Charcoal\Object\Content;
use Charcoal\Object\CategoryInterface;
use Charcoal\Object\CategoryTrait;

// From 'charcoal-cms'
use Charcoal\Cms\Event;

// From `charcoal-validator`
use Charcoal\Validator\ValidatorInterface;

/**
 * Event Category
 */
class EventCategory extends Content implements CategoryInterface
{
    use CategoryTrait;

    /**
     * Translatable
     * @var string[] $name
     */
    protected $name;

    /**
     * Section constructor.
     * @param array $data Init data.
     */
    public function __construct(array $data = null)
    {
        parent::__construct($data);

        if (is_callable([ $this, 'defaultData' ])) {
            $this->setData($this->defaultData());
        }
    }

    /**
     * CategoryTrait > itemType()
     *
     * @return string
     */
    public function itemType()
    {
        return Event::class;
    }

    /**
     * @return \Charcoal\Model\Collection|array
     */
    public function loadCategoryItems()
    {
        return [];
    }

    /**
     * @return mixed
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @param mixed $name The category name.
     * @return self
     */
    public function setName($name)
    {
        $this->name = $this->translator()->translation($name);

        return $this;
    }

    // Events
    // ==========================================================================

    /**
     * @param ValidatorInterface $v Optional. A custom validator object to use for validation. If null, use object's.
     * @return boolean
     */
    public function validate(ValidatorInterface &$v = null)
    {
        parent::validate($v);

        foreach ($this->translator()->locales() as $locale => $value) {
            if (!(string)$this->name()[$locale]) {
                $this->validator()->error(
                    (string)$this->translator()->translation([
                        'fr' => 'Le NOM doit être rempli dans toutes les langues.',
                        'en' => 'The NAME must be filled in all languages.',
                    ])
                );

                return false;
            }
        }

        return true;
    }
}
