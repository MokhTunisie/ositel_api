<?php

namespace App\ApiBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
//use ApiPlatform\Core\Annotation\ApiFilter;
//use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

// * @ApiFilter(SearchFilter::class, properties={"id": "exact", "title": "partial", "description": "partial", "isValid":"exact", "isInput":"exact"})
/**
 * Transaction.
 *
 * @ApiResource(
 *     itemOperations={
 *     "amount": {
 *              "route_name"="monthly_treasury",
 *              "swagger_context" = {
 *                  "responses" = {
 *                      "200" = {
 *                          "description" = "Monthly treasury response",
 *                          "type" = "array",
 *                          "schema" =  {
 *                              "properties" = {"start"={"type"="number"},"end"={"type"="number"}},
 *                          }
 *                      },
 *                      "400" = {
 *                          "description" = "Invalid input"
 *                      },
 *                      "404" = {
 *                          "description" = "resource not found"
 *                      }
 *                  },
 *                  "summary" = "Calculate treasury of the selected month/year",
 *                  "parameters" = {
 *                      {
 *                          "name" = "month",
 *                          "required" = true,
 *                          "type" = "string",
 *                          "in" = "query",
 *                          "description" = "mois Exemple '01' pour Janvier"
 *                      },
 *                      {
 *                          "name" = "year",
 *                          "type" = "string",
 *                          "required" = true,
 *                          "in" = "query",
 *                          "description" = "Anée : Exemple '2019'"
 *                      }
 *                  },
 *                  "produces" = {
 *                      "application/ld+json",
 *                      "application/json"
 *                  }
 *              }
 *           },
 *     "stats": {
 *              "route_name"="monthly_stats",
 *              "swagger_context" = {
 *                  "responses" = {
 *                      "200" = {
 *                          "description" = "Monthly treasury (input or output)",
 *                          "schema" =  {
 *                              "properties" = {"input"={"type"="number"},"output"={"type"="number"}},
 *                          }
 *                      },
 *                      "400" = {
 *                          "description" = "Invalid input"
 *                      },
 *                      "404" = {
 *                          "description" = "resource not found"
 *                      }
 *                  },
 *                  "summary" = "Get treasury input or output of the selected month/year",
 *                  "parameters" = {
 *                      {
 *                          "name" = "month",
 *                          "required" = true,
 *                          "type" = "string",
 *                          "in" = "query",
 *                          "description" = "mois Exemple '01' pour Janvier"
 *                      },
 *                      {
 *                          "name" = "year",
 *                          "type" = "string",
 *                          "required" = true,
 *                          "in" = "query",
 *                          "description" = "Anée : Exemple '2019'"
 *                      }
 *                  },
 *                  "produces" = {
 *                      "application/ld+json",
 *                      "application/json"
 *                  }
 *              }
 *           },
 *          "get"
 *      },
 *     collectionOperations={
 *          "mounthly" = {
 *              "route_name"="monthly_transactions",
 *              "swagger_context" = {
 *                  "parameters" = {
 *                      {
 *                          "name" = "month",
 *                          "required" = true,
 *                          "type" = "string",
 *                          "in" = "query",
 *                          "description" = "mois Exemple '01' pour Janvier"
 *                      },
 *                      {
 *                          "name" = "year",
 *                          "type" = "string",
 *                          "required" = true,
 *                          "in" = "query",
 *                          "description" = "Anée : Exemple '2019'"
 *                      }
 *                  },
 *                  "produces" = {
 *                      "application/ld+json",
 *                      "application/json"
 *                  }
 *              }
 *           }
 *      },
 *     attributes={
 *     "normalization_context"={"groups"={"read"}},
 *     "denormalization_context"={"groups"={"write"}}
 * },
 * )
 *
 * @ORM\Table(name="transactions")
 * @ORM\Entity(repositoryClass="App\ApiBundle\Repository\TransactionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Transaction
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"read", "write"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Groups({"read", "write"})
     */
    private $title;

    /**
     * @ORM\Column(type="float")
     * @Groups({"read", "write"})
     */
    private $amount;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"read", "write"})
     */
    private $isInput;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"read", "write"})
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"read", "write"})
     */
    private $isValid;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read", "write"})
     */
    private $createdAt;

    /**
     * @Groups({"read", "write"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="\App\ApiBundle\Entity\Category", inversedBy="transactions")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")
     * @Groups({"read", "write"})
     */
    private $category;

    /**
     * @ORM\ManyToMany(targetEntity="\App\ApiBundle\Entity\Tag", inversedBy="transactions")
     * @ORM\JoinTable(name="transactions_tags",
     *      joinColumns={@ORM\JoinColumn(name="transaction_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     * )
     * @Groups({"read", "write"})
     */
    private $tags;

    /**
     * Transaction constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Transaction
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return float|null
     */
    public function getAmount(): ?float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     *
     * @return Transaction
     */
    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsInput(): ?bool
    {
        return $this->isInput;
    }

    /**
     * @param bool $isInput
     *
     * @return Transaction
     */
    public function setIsInput(bool $isInput): self
    {
        $this->isInput = $isInput;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return Transaction
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsValid(): ?bool
    {
        return $this->isValid;
    }

    /**
     * @param bool $isValid
     *
     * @return Transaction
     */
    public function setIsValid(bool $isValid): self
    {
        $this->isValid = $isValid;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface|null $createdAt
     *
     * @return Transaction
     */
    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTimeInterface|null $updatedAt
     *
     * @return Transaction
     */
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function lifecyclePersist(): void
    {
        $this->setCreatedAt(new \DateTime('now'));
    }

    /**
     * @ORM\PreUpdate()
     */
    public function lifecycleUpdate(): void
    {
        $this->setUpdatedAt(new \DateTime('now'));
    }

    /**
     * @return \App\ApiBundle\Entity\Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param \App\ApiBundle\Entity\Category|null $category
     *
     * @return Transaction
     */
    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * @param \App\ApiBundle\Entity\Tag $tag
     *
     * @return Transaction
     */
    public function addTag(?Tag $tag): self
    {
        if ($tag && !$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    /**
     * @param \App\ApiBundle\Entity\Tag $tag
     *
     * @return Transaction
     */
    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getTitle() ?: 'Transaction';
    }
}
