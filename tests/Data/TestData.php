<?php

namespace Tests\Data;

class TestData
{
    public function populateDatabase()
    {
        $account = new \Account();
        $account->setName('Cash');
        $account->save();

        $account = new \Account();
        $account->setName('Bank');
        $account->save();

        $user = new \User();
        $user->addAccount($account);
        $user->setFirstName('Bob');
        $user->setLastName('Jones');
        $user->setEmail(new \EmailAddress('bob@example.com'));
        $user->setEnable(true);
        $user->setPassword('really-secure');
        $user->save();

        $hashtagTest = new \Hashtag();
        $hashtagTest->setTag('test');
        $hashtagTest->save();

        $hashtagDifferent = new \Hashtag();
        $hashtagDifferent->setTag('different');
        $hashtagDifferent->save();

        $hashtagSomething = new \Hashtag();
        $hashtagSomething->setTag('something');
        $hashtagSomething->save();

        $transaction = new \Transaction();
        $transaction->setDate(new \DateTime());
        $transaction->setValue(1);
        $transaction->setAccount($account);
        $transaction->setDescription('#test description');
        $transaction->addHashtag($hashtagTest);
        $transaction->save();

        $transaction = new \Transaction();
        $transaction->setDate(new \DateTime());
        $transaction->setValue(-4.05);
        $transaction->setAccount($account);
        $transaction->setDescription('a futher #test description');
        $transaction->addHashtag($hashtagTest);
        $transaction->save();

        $transaction = new \Transaction();
        $transaction->setDate(new \DateTime('- 1 month'));
        $transaction->setValue(10);
        $transaction->setAccount($account);
        $transaction->setDescription('#different description');
        $transaction->addHashtag($hashtagDifferent);
        $transaction->save();

        $transaction = new \Transaction();
        $transaction->setDate(new \DateTime('- 1 month'));
        $transaction->setValue(-3.75);
        $transaction->setAccount($account);
        $transaction->setDescription('another #different description');
        $transaction->addHashtag($hashtagDifferent);
        $transaction->save();

        $transaction = new \Transaction();
        $transaction->setDate(new \DateTime('- 3 months'));
        $transaction->setValue(-403.86);
        $transaction->setAccount($account);
        $transaction->setDescription('this is #something expensive');
        $transaction->addHashtag($hashtagSomething);
        $transaction->save();

        $transaction = new \Transaction();
        $transaction->setDate(new \DateTime('- 1 year'));
        $transaction->setValue(-314.15);
        $transaction->setAccount($account);
        $transaction->setDescription('more #different descriptions');
        $transaction->addHashtag($hashtagDifferent);
        $transaction->save();

        $transaction = new \Transaction();
        $transaction->setDate(new \DateTime('- 1 year'));
        $transaction->setValue(150);
        $transaction->setAccount($account);
        $transaction->setDescription('yet another #different description');
        $transaction->addHashtag($hashtagDifferent);
        $transaction->save();
    }
}
