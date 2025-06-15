<?php

namespace App\Entity;

use App\Repository\ChallengeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChallengeRepository::class)]
class Challenge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $completedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(targetEntity: ChallengeExercise::class, mappedBy: "challenge", cascade: ["persist", "remove"])]
    public Collection $challengeExercises;

    public function __construct()
    {
        $this->challengeExercises = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeImmutable $startedAt): static
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeImmutable $completedAt): static
    {
        $this->completedAt = $completedAt;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getChallengeExercises(): Collection
    {
        return $this->challengeExercises;
    }

    public function addChallengeExercise(ChallengeExercise $exercise): static
    {
        if (!$this->challengeExercises->contains($exercise)) {
            $this->challengeExercises[] = $exercise;
            $exercise->setChallenge($this);
        }
        return $this;
    }

    public function removeChallengeExercise(ChallengeExercise $exercise): static
    {
        if ($this->challengeExercises->removeElement($exercise)) {
            if ($exercise->getChallenge() === $this) {
                $exercise->setChallenge(null);
            }
        }
        return $this;
    }
}
