# How to contribute

Here is the steps to follow to properly help and contribute the project. 

## Step 1 - Set up a copy on your computer

1. Fork the project to your github repositories, by clicking the "Fork" button in upper-right corner.

2. Clone the project
        
        git clone https://github.com/DamienVauchel/todolist.git

3. Change to the new project's directory, example if is at your computer's root

        cd todolist 
        
4. Create a new remote to point to the original project to be able able to grab any changes and bring them into your local copy.
In this example, the new remote is named "upstream"

        git remote add upstream https://github.com/DamienVauchel/todolist.git
        
Then now, you have two remotes for this project on local:
* *origin* : It points to your Github fork of the project so you can read and write to this remote.
* *upstream* : It points to the main project repository so you can only read from to this remote.

## Step 1-bis - Install the project
To install the project on your computer, you can refer to the [README.md](https://github.com/DamienVauchel/todolist/blob/master/README.md)
        
## Step 2 - Work on the project
Now, you can work on the project, solve bugs, etc...

### Branch
**The first rule is to put each piece of work on its own branch.**
For example, you want to update the README.md file:
        
        git checkout master
        git pull upstream master && git push origin master
        git checkout -b hotfix/readme-update
        
So first you checkout your master branch, then you pull the original one and push your own on your repo.
Finally you checkout a new branch called *hotfix/readme-update*.

*Convention's prefixes:*
* *hotfix/* : for updates, fixes
* *feature/* : for new feature

When you did your work, don't forget to add an explicit commit.

## Step 3 - Create the Pull-Request
1. Push your branch on your forked repo

        git push origin hotfix/readme-update
        
2. Go to your Github account's forked repo.
Then click the "Compare & pull request" green button which appeared to be able to write your pull request.
On this page, ensure that the "base fork" points to the correct repository and branch. Then ensure that you provide a good, succinct title for your pull request and explain why you have created it in the description box. Add any relevant issue numbers if you have them.

Once you are happy of your pull request explanation and you have verified your changed, you can press the "Create pull request" button and you're done!

## Step 4: Review by the maintainers
For your work to be integrated into the project, the maintainers will review your work and either request changes or merge it.

If you have any question, you can contact me

Thanks!
