<?php
namespace Bluethink\Crud\Api\Data;

interface ViewInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID                 = 'id';
    const PROFILE_PHOTO      = 'profile_photo';
    const NAME               = 'name';
    const SCORE              = 'score';
    const REMARKS            = 'remarks';
    const CREATED_AT         = 'created_at';
    const UPDATED_AT         = 'updated_at';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Profile photo
     *
     * @return string|null
     */
    public function getProfilePhoto();

    /**
     * Get Title
     *
     * @return string|null
     */
    public function getName();

    /**
     * Get Score
     *
     * @return string|null
     */
    public function getScore();

    /**
     * Get Remarks
     *
     * @return string|null
     */
    public function getRemarks();

    /**
     * Get Created At
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Get Updated At
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
    * Set ID
    *
    * @param int $id
    * @return $this
    */
    public function setId($id);

    /**
     * Set Name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Set Scroe
     *
     * @param string $score
     * @return $this
     */
    public function setScore($score);

    /**
     * Set Remarks
     *
     * @param string $remarks
     * @return $this
     */
    public function setRemarks($remarks);

    /**
     * Set Created At
     *
     * @param int $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Set Updated At
     *
     * @param int $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);
}
