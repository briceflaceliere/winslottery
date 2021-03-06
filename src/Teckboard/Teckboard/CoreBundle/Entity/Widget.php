<?php
/**
 * Created by PhpStorm.
 * User: Brice
 * Date: 21/02/2015
 * Time: 18:02
 */

namespace Teckboard\Teckboard\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Teckboard\Teckboard\CoreBundle\Entity\Traits\CreateByTrait;
use Teckboard\Teckboard\CoreBundle\Entity\Traits\IdTrait;
use Teckboard\Teckboard\CoreBundle\Entity\Traits\NameTrait;
use Teckboard\Teckboard\CoreBundle\Entity\Traits\GridTrait;
use Teckboard\Teckboard\CoreBundle\Entity\Traits\TimestampableTrait;
use JMS\Serializer\Annotation as JMS;
use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Widget
 * @package Teckboard\Teckboard\CoreBundle\Entity
 *
 * @JMS\ExclusionPolicy("all")
 * @ORM\Entity(repositoryClass="Teckboard\Teckboard\CoreBundle\Repository\WidgetRepository")
 *
 * @Hateoas\Relation(
 *          "self",
 *          href = @Hateoas\Route("api_widget_get_widgets", parameters = {"id" = "expr(object.getId())" })
 * )
 */
class Widget
{
    use IdTrait, TimestampableTrait, NameTrait, CreateByTrait, GridTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Board", inversedBy="widgets")
     * @ORM\JoinColumn(name="board_id", referencedColumnName="id", nullable=true)
     *
     * @var Board board
     **/
    protected $board;

    /**
     * @return Board
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * @param Board $board
     *
     * @return $this
     */
    public function setBoard($board)
    {
        $this->board = $board;
        return $this;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Connector", inversedBy="widgets")
     * @ORM\JoinColumn(name="connector_id", referencedColumnName="id", nullable=false)
     *
     * @JMS\Groups({"BoardDetail"})
     * @JMS\Expose
     *
     * @var Connector $connector
     **/
    protected $connector;

    /**
     * @return Connector
     */
    public function getConnector()
    {
        return $this->connector;
    }

    /**
     * @param Connector $connector
     *
     * @return $this
     */
    public function setConnector($connector)
    {
        $this->connector = $connector;
        return $this;
    }

    /**
     * @var integer
     */
    protected $expire;

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("expire")
     *
     * @return string
     */
    public function getExpire()
    {
        if ($this->expire == null) {
            $this->expire = (int)(time() + (60 * 60 * 2));
        }
        return $this->expire;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("token")
     *
     * @return string
     */
    public function getToken()
    {
        return hash('sha1', $this->getId().$this->getExpire().$this->getConnector()->getPrivateKey());
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("url")
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->getConnector()->getUrl() . '?id=' . $this->getId() . '&token=' . $this->getToken() . '&expire=' . $this->getExpire();
    }

}
