# elasticsearch-php-etl

Add PHP class to you project for manage **ETL**, not only for Elasticsearch.
This library is design to be use with symfony components or in symfony full stack frameworks, see the symfony section.

For more convenient way, tests are performs in the [symfony-elasticsearch-integration](https://github.com/NicolasBadey/symfony-elasticsearch-integration)  repository 

## What is it ?

An ETL (Extract => Transform => Load) is used to populate an storage with another source of data and transform the data between the two.
Each one of the three classes can be change without impact on the two others, they are uncoupled.

Basic usage here : SQL(ORM) to Elasticsearch

The important things is to see the Transform layer as a denormalizing layer and do not search to put the same structure in Elasticsearch that in SQL.


## Install

```bash
composer require nicolasbadey/elasticsearch-php-etl
```

## Usage

This library is a set of abstract class, interface and one class for help you to create your ETL and focus only on the business code.

For Symfony dependency injection or other dependency injection system you will need a *builder* with only a constructor that extends `AbstractETLBuilder`, he will create a ETL object.
You Builder live in your code so in Symfony it will be an autowiring service.

The arguments of your builder will be :
 - an Extract that implements `ExtractInterface`
 - an Transform that extends `AbstractTransform` or implements `TransformInterface`
 - an Load that extends `AbstractElasticSearchLoad` for Elasticsearch or if not Elasticsearch `LoadInterface`

`AbstractElasticSearchLoad` will need a parameters that implements `ElasticsearchClientInterface`
Basically a service wrapper of the elasticsearch-php client.

At the end you will do :
```php
$ETL::create()->setExtract($extract)
    ->setTransform($transform)
    ->setLoad($load)
    ->run($output);
```

run in ETL class take an Symfony Console components output

You can index a set of ids with `->run($output,false,[42,43,44]);`
Second arguments is the `live` flag that allow to index directly in the current user index, see below.

### Live mode
Live mode allow indexing directly in the current index if exists or to create a new index with an alias without wait ETL end's
The point is to show content as fast as possible without wait indexation's end.

Basically it's the panic button when ES server has been reset/delete directly in prod (sorry for you).
Or, less stressfull, for update index without have a mapping or deletion changes.

### Index one (Subscriber/Listener)

For index only one documents, you can do it with `run` in live mode and with ids parameter but this method have some advantages :

indexOne don't use Extract layer, so in some conditions like with ORM if you already have the object and your model is not too consumer in lazy loading, it can be an advantage.
If index not exists indexOne don't index the object.

```php
$ETL::create()->setExtract($extract)
    ->setTransform($transform)
    ->setLoad($load)
    ->indexOne($object);
```
Note that for transform your object, `public function transformObject($object): array;` of your Load class will be call.

The second optional parameter of indexOne allow to force Index creation if not exits :

`->indexOne($object, true) ` for enable it

## Technically

PagerFanta is used to iterate in ETL class and as a decoupling layer, so according to the interface your Load class will return an PagerFantaAdapter.

You can use Serialiser Components in Transform layer, if you accept to be at least four times slower.
I prefer to use basic associative array for an ETL.

## Symfony
An integration in Symfony is available here :  [symfony-elasticsearch-integration](https://github.com/NicolasBadey/symfony-elasticsearch-integration)
- use the Builder with `AbstractETLBuilder`
- use `AbstractETLCommand` for you ETL command
...
