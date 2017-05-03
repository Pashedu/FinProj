<?php

namespace Softgroup\FinalBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Message
 *
 * @ORM\Table(name="message")
 * @ORM\Entity(repositoryClass="Softgroup\FinalBundle\Repository\MessageRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Message
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="messagetext", type="text")
     */
    private $messagetext;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdate", type="datetime")
     */
    private $createdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deletedate", type="datetime", nullable=true)
     */
    private $deletedate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleteto", type="datetime", nullable=true)
     */
    private $deleteto;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, unique=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=true, unique=true)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @Assert\Email(message = "The email '{{ value }}' is not a valid email.")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="creatorip", type="string", length=255, nullable=true)
     */
    private $creatorip;

    /**
     * @var string
     *
     * @ORM\Column(name="readerip", type="string", length=255, nullable=true)
     */
    private $readerip;

    /**
     * Get id
     *
     * @return integer 
     */

    private $plainPassword;

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainpass)
    {
        $this->plainPassword=$plainpass;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Set messagetext
     *
     * @param string $messagetext
     * @return Message
     */
    public function setMessagetext($messagetext)
    {
        $this->messagetext = $messagetext;

        return $this;
    }

    /**
     * Get messagetext
     *
     * @return string 
     */
    public function getMessagetext()
    {
        return $this->messagetext;
    }

    /**
     * Set createdate
     *
     * @param \DateTime $createdate
     * @return Message
     */
    public function setCreatedate($createdate)
    {
        $this->createdate = $createdate;

        return $this;
    }

    /**
     * Get createdate
     *
     * @return \DateTime 
     */
    public function getCreatedate()
    {
        return $this->createdate;
    }

    /**
     * Set deletedate
     *
     * @param \DateTime $deletedate
     * @return Message
     */
    public function setDeletedate($deletedate)
    {
        $this->deletedate = $deletedate;

        return $this;
    }

    /**
     * Get deletedate
     *
     * @return \DateTime 
     */
    public function getDeletedate()
    {
        return $this->deletedate;
    }

    /**
     * Set deleteto
     *
     * @param \DateTime $deleteto
     * @return Message
     */
    public function setDeleteto($deleteto)
    {
        $this->deleteto = $deleteto;

        return $this;
    }

    /**
     * Get deleteto
     *
     * @return \DateTime 
     */
    public function getDeleteto()
    {
        return $this->deleteto;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Message
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Message
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Message
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }
    /**
     * Set creatorip
     *
     * @param string $ip
     * @return Message
     */
    public function setCreatorip($ip)
    {
        $this->creatorip = $ip;

        return $this;
    }

    /**
     * Get creatorip
     *
     * @return string
     */
    public function getCreatorip()
    {
        return $this->creatorip;
    }
    /**
     * Set readerip
     *
     * @param string $ip
     * @return Message
     */
    public function setReaderip($ip)
    {
        $this->readerip = $ip;

        return $this;
    }
    /**
     * Get readerip
     *
     * @return string
     */
    public function getReaderip()
    {
        return $this->readerip;
    }

    /**
     * @ORM\PrePersist
     */
    function prePersist()
    {
        $this->createdate = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    function getTTL()
    {}

    function setTTL($dataValue)
    {

        switch ($dataValue)
        {
            case '1': $timePeriod='PT1H';break;
            case '2': $timePeriod='PT2H';break;
            case '3': $timePeriod='P1D';break;
            case '4': $timePeriod='P3D';break;
            case '5': $timePeriod='P1W';break;
            default : return $this;
        }
        $this->deleteto = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->deleteto->add(new \DateInterval($timePeriod));
        return $this;
    }
}
