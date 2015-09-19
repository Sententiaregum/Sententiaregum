Contributing Guide Line for Sententiaregum
==========================================

This guideline shows some conventions when contributing to Sententiaregum, so your Pull Request can be handled faster.

1) Code style
-------------

Your patch __must__ follow the [symfony coding standards](http://symfony.com/doc/current/contributing/code/standards.html)

2) Branch name and commit message
---------------------------------

### Branch name

If you'd like to fix an issue, the branch name should look like this: 

    Sententiaregum-{issue key}

If you'd like to add a new feature without an issue, the branch should contain the name of the feature separated with dashes.
So if you add a realtime messenger to this application, the branch could look like this:

    realtime-messenger

### Commit message

#### When fixing a ticket

When fixing an issue, the issue should have a red label (e.g. Infrastructure/Maintenance/Bug).
Then the commit message should look like this:

    {Issue type (e.g. Infrastructure/Maintenance/Bug} #{ticket number} {short description what you've done}

If there's no red label at the ticket, the issue type is __Minor__.

#### When adding a new feature

As there are no labels, the commit message should look like this:

    [Feature] {short description what you've done}

If there's a label added to your PR, the commit message should be renamed to the following pattern:

    {Issue Type (red label)} {short description what you've done}

3) Pull Request
---------------

The pull request should have some content, too.
You just need to tell what has changed.

__Note__: if you have fixed a bug that is not reported in the issue tracker, please add a detailed description how this bug was caused.

#### Reviews

If a line note is added, you should fix that. If there's a reason why that should not be fixed, please comment on the diff, too.
If the review is ready and at least one collaborator or owner gave thumbs up, you should squash all of your commit into a big one.
