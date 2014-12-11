<?php
namespace Gbili\Miner\Entity

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @todo trying to move out of the Savable Crap to Doctrine 2.
 * There are many things to sort out: 
 *   The fact that:
 *     there are many action types 
 *     AND there is a tree structure between these actions
 *     AND doctrine 2 does not support many to many relations for mappedSuperclasses 
 *   is a problem.
 * 1. Action Extract has:
 *   - useMatchAll
 *   - GroupResultMapping (Group Result Mapping)
 * 2. Action GetContents has:
 *   - Callback Map (callbackMap has a control to make sure array starts from 0..length-1)
 *        i.e. array_keys($mapping) !== range(0, count($mapping) - 1)
 *   - Callback Method (Currently uses CM loader, but should be replace by autoloading)
 *
 * ActionConfig
 *
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="action_config")
 * @ORM\Entity(repositoryClass="Blog\Entity\Repository\NestedTreeFlat")
 */
class ActionConfig
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Type: "GetContents" or "Extract"
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=64, nullable=false, unique=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=64, nullable=false, unique=false)
     */
    private $title;

    /**
     * Only Meaningfull when action is of type extract
     *
     * @var boolean 
     *
     * @ORM\Column(name="useMatchAll", type="boolean", nullable=true, unique=false, options={"default":false})
     */
    private $useMatchAll;

    /**
     * @var boolean 
     *
     * @ORM\Column(name="optional", type="boolean", nullable=false, unique=false, options={"default":false})
     */
    private $optional;

    /**
     * @var boolean 
     *
     * @ORM\Column(name="isNewInstanceGeneratingPoint", type="boolean", nullable=false, unique=false, options={"default":false})
     */
    private $isNewInstanceGeneratingPoint;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=false, unique=false)
     */
    private $data;

    /**
     * @ORM\ManyToOne(targetEntity="Blueprint", fetch="EAGER")
     * @ORM\JoinColumn(name="blueprint_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $blueprint;

    /**
     * Only meaninful if has parent, and parent is Extract
     * From which parent regex group (in case of parent extract) should this action take input from
     * If parent is not extract then the the input parent regex group should be null
     *
     * @var string
     *
     * @ORM\Column(name="inputParentRegexGroup", type="string", length=64, nullable=true, unique=false)
     */
    private $inputParentRegexGroup;

    /**
     * @var integer
     *
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $lft;

    /**
     * @var integer
     *
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $lvl;

    /**
     * @var integer
     *
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $rgt;

    /**
     * @var integer
     *
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $root;

    /**
     * @var ActionConfig
     *
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="ActionConfig", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="ActionConfig", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type of action
     *
     * @param string $type
     * @return ActionConfig
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return ActionConfig
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set useMatchAll only if action is of type extract
     *
     * @param boolean $useMatchAll
     * @return ActionConfig
     */
    public function setUseMatchAll($useMatchAll=null)
    {
        $this->useMatchAll = $useMatchAll;
        return $this;
    }

    /**
     * Get useMatchAll
     *
     * @return boolean
     */
    public function getUseMatchAll()
    {
        return $this->useMatchAll;
    }

    /**
     * Set inputParentRegexGroup only meaningful 
     * if has parent and parent is Extract
     * 
     * @refactoring inputParentRegexGroup default if parent is Extract = 1, else 0
     *
     * @param string $inputParentRegexGroup
     * @return ActionConfig
     */
    public function setInputParentRegexGroup($inputParentRegexGroup=0)
    {
        $this->inputParentRegexGroup = $inputParentRegexGroup;
        return $this;
    }

    /**
     * Get inputParentRegexGroup
     *
     * @return string 
     */
    public function getInputParentRegexGroup()
    {
        return $this->inputParentRegexGroup;
    }

    /**
     * Set data
     *
     * @param string $data
     * @return string 
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get data
     *
     * @return string 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set isNewInstanceGeneratingPoint
     *
     * @param boolan 
     * @return ActionConfig
     */
    public function setIsNewInstanceGeneratingPoint($bool)
    {
        $this->isNewInstanceGeneratingPoint = (boolean) $bool;
        return $this;
    }

    /**
     * Get isNewInstanceGeneratingPoint
     *
     * @return ActionConfig
     */
    public function isNewInstanceGeneratingPoint()
    {
        return $this->isNewInstanceGeneratingPoint;
    }

    /**
     * Set optional
     *
     * @param string $optional
     * @return ActionConfig
     */
    public function setIsOptional($optional)
    {
        $this->optional = $optional;
        return $this;
    }

    /**
     * Get optional
     *
     * @return string 
     */
    public function getIsOptional()
    {
        return $this->isOptional;
    }

    /**
     * Set this action's blueprint
     *
     * @return ActionConfig 
     */
    public function setBlueprint(Blueprint $bp)
    {
        $this->blueprint = $bp;
        return $this;
    }

    /**
     * This action's blueprint
     *
     * @return Blueprint 
     */
    public function getBlueprint()
    {
        return $this->blueprint;
    }

    /**
     * Set lft
     *
     * @param integer $lft
     * @return ActionConfig
     */
    public function setLft($lft)
    {
        $this->lft = $lft;
        return $this;
    }

    /**
     * Get lft
     *
     * @return integer 
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set lvl
     *
     * @param integer $lvl
     * @return ActionConfig
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;
        return $this;
    }

    /**
     * Get lvl
     *
     * @return integer 
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Set rgt
     *
     * @param integer $rgt
     * @return ActionConfig
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;
        return $this;
    }

    /**
     * Get rgt
     *
     * @return integer 
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set root
     *
     * @param integer $root
     * @return ActionConfig
     */
    public function setRoot($root)
    {
        $this->root = $root;
        return $this;
    }

    /**
     * Get root
     *
     * @return integer 
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Set parent
     *
     * @param ActionConfig $parent
     * @return ActionConfig
     */
    public function setParent(ActionConfig $parent=null)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Get parent
     *
     * @return ActionConfig 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * (If has no parent then shoold be root too)
     * @return boolean
     */
    public function hasParent()
    {
        return $this->getParent() !== null;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function addChild(ActionConfig $child)
    {
        $this->reuseLocales($this, $child);
        $child->setParent($this);
        $this->children->add($child);
    }

    public function addChildren(\Doctrine\Common\Collections\Collection $children)
    {
        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    public function removeChild(ActionConfig $child)
    {
        $child->setParent(null);
        $this->children->removeElement($child);
    }

    public function removeChildren(\Doctrine\Common\Collections\Collection $children)
    {
        foreach ($children as $child) {
            $this->removeChild($child);
        }
    }
}
