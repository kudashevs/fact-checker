# A fact checking application

This is an example app for my presentation "[Mocks aren’t Stubs. What are test doubles and how do we use them?](https://www.youtube.com/watch?v=5JQ-Pn9Ob-w)"


## Description

The goal of the application is to show how we can use different kinds of **test doubles** in different situations. The app provides a random
interesting fact about cats by retrieving it from a public API, evaluating the truthfulness of this fact, and calculating a score.

**How it works**: We fetch a fact about cats from the public API. Then, we use an algorithm to assess the reliability of the fact, based on
a fact’s score. Finally, the app generates an output that includes the fact with the opinion and the score.

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

**A simplified class diagram**
![simplified-diagram](https://github.com/kudashevs/fact-checker/assets/15892462/8f19db77-3240-4202-9bcd-613e922dd863)

**A simplified sequence diagram**
![sequence-diagram](https://github.com/kudashevs/fact-checker/assets/15892462/b123955d-fee2-4ccd-bf4d-ecce9dd01d99)

**Disclaimer** This application is supposed to be as demonstrative as possible. Therefore, it lacks separation of concerns (responsibilities) with clear boundaries,
a flexible output, a mechanism for managing dependencies, etc. On the other hand, the app's logic is gathered in one place - the **randomFact()** method and its internal calls.


## Tests

Let's examine possible test cases for our requirements: 

**Fetcher** component
- an expected JSON (happy path)
- error requesting data with unspecified reason (fail path)
- error requesting data due to a timeout (fail path)
- an empty JSON (exceptional condition)
- an invalid JSON (exceptional condition)
- an unexpected JSON (exceptional condition/edge case)

**Assessor** component
- an expected string (happy path)
- an empty string (edge case)

### List of tests

| **Fetcher** component | Test doubles |
| :------------- | :------------- |
| an expected JSON | stub |
| error requesting data with unspecified reason | stub / mock (for logging only) |
| error requesting data due to a timeout | stub / mock (for logging only) |
| an empty JSON | stub / mock (for logging only) |
| an invalid JSON | stub / mock (for logging only) |
| an unexpected JSON | stub / mock (for logging only) / spy (for notifier) |
|<img width="640" height="1"/>|<img width="320" height="1"/>|

| **Assessor** component | Test doubles |
| :------------- | :------------- |
| an expected string | stub / real implementation / never mock |
| an empty string | stub / real implementation |
|<img width="640" height="1"/>|<img width="320" height="1"/>|


## License

The MIT License (MIT). Please see the [License file](LICENSE.md) for more information.