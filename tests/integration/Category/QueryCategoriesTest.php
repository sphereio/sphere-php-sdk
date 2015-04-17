<?php
/**
 * @author @ct-jensschulze <jens.schulze@commercetools.de>
 */

namespace Sphere\Core\Category;


use Sphere\Core\ApiTestCase;
use Sphere\Core\Model\Category\Category;
use Sphere\Core\Model\Category\CategoryDraft;
use Sphere\Core\Model\Category\CategoryReference;
use Sphere\Core\Model\Common\LocalizedString;
use Sphere\Core\Request\Categories\CategoriesQueryRequest;
use Sphere\Core\Request\Categories\CategoryCreateRequest;
use Sphere\Core\Request\Categories\CategoryDeleteByIdRequest;
use Sphere\Core\Request\Categories\CategoryFetchByIdRequest;

class QueryCategoriesTest extends ApiTestCase
{
    /**
     * @param $name
     * @param $slug
     * @return CategoryDraft
     */
    protected function getDraft($name, $slug)
    {
        $draft = CategoryDraft::of(
            LocalizedString::of(['en' => $name]),
            LocalizedString::of(['en' => $slug])
        );

        return $draft;
    }

    protected function createCategory(CategoryDraft $draft)
    {
        /**
         * @var Category $category
         */
        $category = $this->getClient()
            ->execute(CategoryCreateRequest::of($draft))
            ->toObject();
        $this->cleanupRequests[] = CategoryDeleteByIdRequest::of($category->getId(), $category->getVersion());

        return $category;
    }

    public function testQueryByName()
    {
        $category = $this->createCategory($this->getDraft('myCategory', 'my-category'));

        $result = $this->getClient()->execute(CategoriesQueryRequest::of()->where('name(en="myCategory")'))->toObject();

        $this->assertCount(1, $result);
        $this->assertInstanceOf('\Sphere\Core\Model\Category\Category', $result->getAt(0));
        $this->assertSame($category->getId(), $result->getAt(0)->getId());
    }

    public function testQueryByNotName()
    {
        $this->createCategory($this->getDraft('myCategory', 'my-category'));

        $result = $this->getClient()->execute(
            CategoriesQueryRequest::of()->where('not(name(en="myCategory"))')
        )->toObject();

        $this->assertCount(0, $result);
    }

    public function testQueryByExternalId()
    {
        $category = $this->createCategory($this->getDraft('myCategory', 'my-category')->setExternalId('myExternalId'));

        $result = $this->getClient()->execute(
            CategoriesQueryRequest::of()->where('externalId="myExternalId"')
        )->toObject();

        $this->assertCount(1, $result);
        $this->assertInstanceOf('\Sphere\Core\Model\Category\Category', $result->getAt(0));
        $this->assertSame($category->getId(), $result->getAt(0)->getId());
    }

    public function testQueryHierarchy()
    {
        $parent = $this->createCategory($this->getDraft('parentCategory', 'parent-category'));
        $child = $this->createCategory(
            $this->getDraft('childCategory', 'child-category')
                ->setParent(CategoryReference::of($parent->getId()))
        );

        $this->assertSame('childCategory', $child->getName()->en);
        $this->assertSame('parentCategory', $parent->getName()->en);

        $result = $this->getClient()->execute(
            CategoriesQueryRequest::of()->where('parent(id="'.$parent->getId().'")')
        )->toObject();

        $this->assertSame($child->getId(), $result->getAt(0)->getId());

        $result = $this->getClient()->execute(
            CategoriesQueryRequest::of()->where('parent is defined')
        )->toObject();
        $this->assertSame($child->getId(), $result->getAt(0)->getId());

        $result = $this->getClient()->execute(
            CategoriesQueryRequest::of()->where('parent is not defined')
        )->toObject();
        $this->assertSame($parent->getId(), $result->getAt(0)->getId());
    }

    public function testAncestorExpansion()
    {
        $level1 = $this->createCategory($this->getDraft('level1', 'level1'));
        $level2 = $this->createCategory(
            $this->getDraft('level2', 'level2')
                ->setParent(CategoryReference::of($level1->getId()))
        );
        $level3 = $this->createCategory(
            $this->getDraft('level3', 'level3')
                ->setParent(CategoryReference::of($level2->getId()))
        );
        $level4 = $this->createCategory(
            $this->getDraft('level4', 'level4')
                ->setParent(CategoryReference::of($level3->getId()))
        );

        /**
         * @var Category $result
         */
        $result = $this->getClient()->execute(
            CategoryFetchByIdRequest::of($level4->getId())->expand('ancestors[*].ancestors[*]')
        )->toObject();

        $this->assertCount(3, $result->getAncestors());

        $ancestorIds = $this->map(
            function ($value) {
                return $value->getObj()->getId();
            },
            $result->getAncestors()
        );
        $expectedAncestors = [$level1->getId(), $level2->getId(), $level3->getId()];
        $this->assertSame($expectedAncestors, $ancestorIds);

        $level3ExpandedAncestor = $result->getAncestors()->getAt(2)->getObj();
        $this->assertSame($level3->getId(), $level3ExpandedAncestor->getId());

        $this->assertSame($level1->getId(), $level3ExpandedAncestor->getAncestors()->getAt(0)->getObj()->getId());
    }

    public function testParentExpansion()
    {
        $level1 = $this->createCategory($this->getDraft('level1', 'level1'));
        $level2 = $this->createCategory(
            $this->getDraft('level2', 'level2')
                ->setParent(CategoryReference::of($level1->getId()))
        );

        /**
         * @var Category $result
         */
        $result = $this->getClient()->execute(
            CategoryFetchByIdRequest::of($level2->getId())->expand('parent')
        )->toObject();
        $this->assertSame($level1->getId(), $result->getParent()->getObj()->getId());
    }

    protected function predicateTestCase($predicate)
    {
        $this->createCategory($this->getDraft('1', '1'));
        $draft = $this->getDraft('2', '2');
        $draft->getName()->add('cn', 'x');
        $this->createCategory($draft);
        $this->createCategory($this->getDraft('10', '10'));

        return $this->getClient()->execute(
            CategoriesQueryRequest::of()->where($predicate)->sort('createdAt DESC')
        )->toObject();
    }

    public function testGreaterThanComparison()
    {
        $predicate = 'name(en > "1")';
        $result = $this->predicateTestCase($predicate);

        $names = array_flip($this->map(
            function ($value) {
                return $value->getName()->en;
            },
            $result
        ));

        $this->assertArrayHasKey('2', $names);
        $this->assertArrayHasKey('10', $names);
        $this->assertArrayNotHasKey('1', $names);
    }

    public function testLesserThanComparison()
    {
        $predicate = 'name(en < "2")';
        $result = $this->predicateTestCase($predicate);

        $names = array_flip($this->map(
            function ($value) {
                return $value->getName()->en;
            },
            $result
        ));

        $this->assertArrayHasKey('1', $names);
        $this->assertArrayHasKey('10', $names);
        $this->assertArrayNotHasKey('2', $names);
    }

    public function testLesserThanEqualsComparison()
    {
        $predicate = 'name(en <= "10")';
        $result = $this->predicateTestCase($predicate);

        $names = array_flip($this->map(
            function ($value) {
                return $value->getName()->en;
            },
            $result
        ));

        $this->assertArrayHasKey('1', $names);
        $this->assertArrayHasKey('10', $names);
        $this->assertArrayNotHasKey('2', $names);
    }

    public function testGreaterThanEqualsComparison()
    {
        $predicate = 'name(en >= "10")';
        $result = $this->predicateTestCase($predicate);

        $names = array_flip($this->map(
            function ($value) {
                return $value->getName()->en;
            },
            $result
        ));

        $this->assertArrayHasKey('2', $names);
        $this->assertArrayHasKey('10', $names);
        $this->assertArrayNotHasKey('1', $names);
    }

    public function testNotIn()
    {
        $predicate = 'name(en not in("10", "2"))';
        $result = $this->predicateTestCase($predicate);

        $names = array_flip($this->map(
            function ($value) {
                return $value->getName()->en;
            },
            $result
        ));

        $this->assertArrayNotHasKey('2', $names);
        $this->assertArrayNotHasKey('10', $names);
        $this->assertArrayHasKey('1', $names);
    }

    public function testIsDefined()
    {
        $predicate = 'name(cn is defined)';
        $result = $this->predicateTestCase($predicate);

        $names = array_flip($this->map(
            function ($value) {
                return $value->getName()->en;
            },
            $result
        ));

        $this->assertArrayNotHasKey('1', $names);
        $this->assertArrayNotHasKey('10', $names);
        $this->assertArrayHasKey('2', $names);
    }

    public function testIsNotDefined()
    {
        $predicate = 'name(cn is not defined)';
        $result = $this->predicateTestCase($predicate);

        $names = array_flip($this->map(
            function ($value) {
                return $value->getName()->en;
            },
            $result
        ));

        $this->assertArrayHasKey('1', $names);
        $this->assertArrayHasKey('10', $names);
        $this->assertArrayNotHasKey('2', $names);
    }

    public function testOverpaging()
    {

    }
}
