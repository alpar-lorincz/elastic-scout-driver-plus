<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration;

use ElasticScoutDriverPlus\Tests\App\Book;
use Illuminate\Database\Eloquent\Model;

/**
 * @covers \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\IdsQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @covers \ElasticScoutDriverPlus\CustomSearch
 * @covers \ElasticScoutDriverPlus\Decorators\EngineDecorator
 *
 * @uses   \ElasticScoutDriverPlus\Builders\QueryParameters\Collection
 * @uses   \ElasticScoutDriverPlus\Builders\QueryParameters\Transformers\FlatArrayTransformer
 * @uses   \ElasticScoutDriverPlus\Builders\QueryParameters\Validators\AllOfValidator
 * @uses   \ElasticScoutDriverPlus\Factories\LazyModelFactory
 * @uses   \ElasticScoutDriverPlus\Factories\SearchResultFactory
 * @uses   \ElasticScoutDriverPlus\SearchMatch
 * @uses   \ElasticScoutDriverPlus\SearchResult
 * @uses   \ElasticScoutDriverPlus\Support\ModelScope
 */
final class IdsSearchTest extends TestCase
{
    public function test_models_can_be_found_by_ids(): void
    {
        $models = collect(range(1, 10))->map(static function (int $i): Model {
            return factory(Book::class)
                ->state('belongs_to_author')
                ->create(['id' => $i]);
        });

        $target = $models->where('id', '>', 7)->values();

        $found = Book::idsSearch()
            ->values(['8', '9', '10'])
            ->execute();

        $this->assertCount($target->count(), $found->models());
        $this->assertEquals($target->toArray(), $found->models()->toArray());
    }
}
