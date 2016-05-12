# Liquid Democracy Experiment

This is a personal open-sourced project to learn the use of the Laravel framework

The project is a web application to create and manage any kind of community using a [Delegative Democracy](https://www.wikiwand.com/en/Delegative_democracy) system.

Delegative democracy, also known as liquid democracy, is a form of democratic control whereby an electorate vests voting power in delegates rather than in representatives.

At first, let me explain why it's an experimental project:

 With this web app you can create and join every community you want governed by a delegative democracy system, but if you want to use it in a real enviroment you should perform some features to confirm the user's identity and some kind of access control system for private communities.

As you can see the entity named 'Community' is the core of the entire Database diagram, even wrapping the 'User' entity in a single membership community that inherits all his capabilities. This allows a user to belong to several independent communities.

With this squema, action to delegate the vote is between entities of type 'community'.

This way the database diagram is ready for a future improvement that allows to create inner communities (lobbies) with their own rules in which users can delegate their votes. I invite anyone to create a branch to perform this feature.

![Database diagram](https://raw.githubusercontent.com/garabaya/LDE/master/resources/assets/diagram.png?raw=true "Database diagram")

## License

Open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
