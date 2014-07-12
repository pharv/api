<?php
/**
 * This file is part of the Tmdb PHP API created by Michael Roterman.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Tmdb
 * @author Michael Roterman <michael@wtfz.net>
 * @copyright (c) 2013, Michael Roterman
 * @version 0.0.1
 */
namespace Tmdb\Factory;

use Tmdb\Common\ObjectHydrator;
use Tmdb\Model\AbstractModel;
use Tmdb\Model\Collection\ResultCollection;
use Tmdb\Model\Common\AccountStates;
use Tmdb\Model\Common\GenericCollection;
use Tmdb\Model\Common\Rating;
use Tmdb\Model\Lists\Result;

/**
 * Class AbstractFactory
 * @package Tmdb\Factory
 */
abstract class AbstractFactory
{
    /**
     * Convert an array to an hydrated object
     *
     * @param  array         $data
     * @return AbstractModel
     */
    abstract public function create(array $data = array());

    /**
     * Convert an array with an collection of items to an hydrated object collection
     *
     * @param  array             $data
     * @return GenericCollection
     */
    abstract public function createCollection(array $data = array());

    /**
     * Create a generic collection of data and map it on the class by it's static parameter $properties
     *
     * @param  array             $data
     * @param $class
     * @return GenericCollection
     */
    protected function createGenericCollection($data = array(), $class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $collection = new GenericCollection();

        if (null === $data) {
            return $collection;
        }

        foreach ($data as $item) {
            $collection->add(null, $this->hydrate(new $class(), $item));
        }

        return $collection;
    }

    /**
     * Create a result collection
     *
     * @param  array            $data
     * @param  string           $method
     * @return ResultCollection
     */
    public function createResultCollection($data = array(), $method = 'create')
    {
        $collection = new ResultCollection();

        if (null === $data) {
            return $collection;
        }

        if (array_key_exists('page', $data)) {
            $collection->setPage($data['page']);
        }

        if (array_key_exists('total_pages', $data)) {
            $collection->setTotalPages($data['total_pages']);
        }

        if (array_key_exists('total_results', $data)) {
            $collection->setTotalResults($data['total_results']);
        }

        if (array_key_exists('results', $data)) {
            $data = $data['results'];
        }

        foreach ($data as $item) {
            $collection->add(null, $this->$method($item));
        }

        return $collection;
    }

    /**
     * Create a generic collection of data and map it on the class by it's static parameter $properties
     *
     * @param  array             $data
     * @param  AbstractModel     $class
     * @param  GenericCollection $collection
     * @return GenericCollection
     */
    protected function createCustomCollection($data = array(), $class, $collection)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        if (null === $data) {
            return $collection;
        }

        foreach ($data as $item) {
            $collection->add(null, $this->hydrate(new $class(), $item));
        }

        return $collection;
    }

    /**
     * Create rating
     *
     * @param  array                     $data
     * @return \Tmdb\Model\AbstractModel
     */
    public function createRating(array $data = array())
    {
        return $this->hydrate(new Rating(), $data);
    }

    /**
     * Create the account states
     *
     * @param  array                     $data
     * @return \Tmdb\Model\AbstractModel
     */
    public function createAccountStates(array $data = array())
    {
        $accountStates = new AccountStates();

        if (array_key_exists('rated', $data)) {
            $rating = new Rating();

            $accountStates->setRated($this->hydrate($rating, $data['rated']));
        }

        return $this->hydrate($accountStates, $data);
    }

    /**
     * Create result
     *
     * @param  array                     $data
     * @return \Tmdb\Model\AbstractModel
     */
    public function createResult(array $data = array())
    {
        return $this->hydrate(new Result(), $data);
    }

    /**
     * Hydrate the object with data
     *
     * @param  AbstractModel $object
     * @param  array         $data
     * @return AbstractModel
     */
    protected function hydrate(AbstractModel $object, $data = array())
    {
        $objectHydrator = new ObjectHydrator();

        return $objectHydrator->hydrate($object, $data);
    }
}
