<?php

namespace TechWilk\Money\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TechWilk\Money\HashtagQuery;

class TagController extends AbstractController
{
    public function getTags(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $this->logger->info("Fetch tags GET '/tags'");

        $tags = HashtagQuery::create()->orderByTag()->find();

        $topHashtags = HashtagQuery::create()
                    ->useTransactionHashtagQuery()
                        ->withColumn('COUNT(*)', 'Count')
                        ->select(['Transaction', 'Count'])
                    ->endUse()
                    ->groupByTag()
                    ->orderByCount('desc')
                    ->limit(5);
        $newestHashtags = HashtagQuery::create()->orderById('desc')->limit(5)->find();

        $recentHashtags = HashtagQuery::create()->lastUsedHashtagsForUser($this->auth->currentUser());

        return $this->view->render($response, 'tags.twig', [
            'tags'   => $tags,
            'newest' => $newestHashtags,
            'top'    => $topHashtags,
            'recent' => $recentHashtags,
        ]);
    }

    public function getTagsJson(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $this->logger->info("Fetch tags GET '/tags'");

        $query = $request->getQueryParams('q');
        //$query = strtolower($query['q']);

        $tags = HashtagQuery::create()->orderByTag()->find();

        // if (isset($query))
        // {
        //     $tags = HashtagQuery::create()->where('Hashtag.Tag LIKE ?', '%'.$query.'%')->orderByTag()->toString();
        // }

        $tagNamesArray = [];

        foreach ($tags as $tag) {
            $tagNamesArray[$tag->getTag()] = [$tag->getTag(), $tag->countTransactions()];
        }

        if (!empty($query['q'])) {
            foreach ($tagNamesArray as $key => $tag) {
                if (!(strpos($tag[0], strtolower($query['q'])) !== false)) {
                    unset($tagNamesArray[$key]);
                }
            }
        }

        return $response->withJson($tagNamesArray);
    }

    public function getTag(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $this->logger->info("Fetch tag GET '/tag/".$args['tag']."'");

        $transactions = TransactionQuery::create()->forCurrentUser($this)->useTransactionHashtagQuery()->useHashtagQuery()->filterByTag(strtolower($args['tag']))->endUse()->endUse()->orderByDate('desc')->find();

        return $this->view->render($response, 'transactions.twig', ['transactions' => $transactions, 'tagName' => $args['tag']]);
    }
}
