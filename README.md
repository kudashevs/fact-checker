# A fact checking application

This is an example app for my presentation "Mocks aren’t Stubs. What are test doubles and how do we use them?"


## Description

The goal of the application is to show how we can use different **test doubles** in different situations. The app provides a random
interesting fact about cats using an API, evaluates the truthfulness of this fact, and calculates a score.

**How it works**: We fetch a fact about cats from the public API. Then, we use an algorithm to check the reliability of the fact,
and we give a fact’s score. Then, the app generates an output that includes the fact with the score and the opinion.


### Requirements

We are going to cover only few cases from the requirements:

* should fetch a fact about cats
* should assess the fact’s truthfulness and give a score
* should log incorrect interactions with the public API
* when a received JSON is unexpected (a business requirement):
  - notify ‘CTO’ about the problem via email
  - notify ‘programmers’ about the problem via email
  - notify ‘programmers’ about the problem via slack
* ...


## License

The MIT License (MIT). Please see the [License file](LICENSE.md) for more information.