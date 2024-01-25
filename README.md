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

**A simplified class diagram**
![simplified-diagram](https://github.com/kudashevs/fact-checker/assets/15892462/f2a6e46e-0707-4ae2-8ce9-a9e066a90b98)

**A simplified sequence diagram**
![sequence-diagram](https://github.com/kudashevs/fact-checker/assets/15892462/b123955d-fee2-4ccd-bf4d-ecce9dd01d99)


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