## Sample API starter based on Symfony4
For recruitment purposes

### Stack
1. Symfony 4.4
2. MongoDB
3. NelmioApiDoc
4. PHPStan
5. PHP CS Fixer

### Testing
Already created group of helpers to boosting implementing and testing new API endpoints, 
`./tests` directory for more informations.

### CI
To help you check your code by one line of code run from command line:
`composer ci` it will run PHPUnit tests and make PHPStan validation.

### API DOC
![API doc](https://i.ibb.co/p1Sn33k/Screenshot-2020-03-17-at-20-34-16.png)

### Updating vendor results
To update results, run command: `php bin/console app:update:score`
