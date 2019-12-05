<?php
/**
 *  * Created by PhpStorm.
 * User: sergey_h
 * Date: 14.12.18
 * Time: 8:52
 */

namespace App\Entity\Extra;


/**
 * Interface PeriodicalInterface
 *
 * @package App\Entity\Extra
 */
interface PeriodicalInterface
{
    /**
     * @return \DateTimeInterface|null
     */
    public function getStartDate(): ?\DateTimeInterface;

    /**
     * @param \DateTimeInterface $startDate
     * @return PeriodicalInterface
     */
    public function setStartDate(\DateTimeInterface $startDate);

    /**
     * @return \DateTimeInterface|null
     */
    public function getFinishDate(): ?\DateTimeInterface;

    /**
     * @param \DateTimeInterface $finishDate
     * @return PeriodicalInterface
     */
    public function setFinishDate(\DateTimeInterface $finishDate);

}