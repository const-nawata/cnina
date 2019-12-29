<?php
namespace App\DTO;

class DeleteEntityDto {

	/**
	 * id of record which must be deleted.
	 *
	 * @var int
	 * @Assert\NotBlank(message="entity.field.not_blank")
	 */
	private $id;

	/**
	 * Entity class name.
	 * @var static
	 * @Assert\NotBlank(message="entity.field.not_blank")
	 */
	private $entityName;

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return $this
	 */
	public function setId( int $id ): self
	{
		$this->id	= $id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEntityName(): string
	{
		return $this->entityName;
	}

	/**
	 * @param string $entityName
	 * @return $this
	 */
	public function setEntityName( string $entityName ): self
	{
		$this->entityName	= $entityName;
		return $this;
	}
}