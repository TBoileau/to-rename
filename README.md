# Twig

## Présentation
Application pour gérer ma création de contenu sur Youtube et surtout Twitch.

## Documentation
Pré-requis :
* PHP 8.1

Installation:
```
make install db_user=root db_password=password db_name=twitch db_host=127.0.0.1 google_api_key=API_KEY google_client_id=CLIENT_ID google_client_secret=CLIENT_SECRET twitter_api_key=API_KEY twitter_api_secret=API_SECRET twitter_token=TOKEN
```

Base de données
```
make prepare env=DEV|TEST
```

Auto correction du code :
```
make fix
```

Analyse du code :
```
make analyse
```

## Diagramme de classes
```mermaid
classDiagram
    class Video {
        - ?int id
        - string title
        - int season
        - int episode
        - string description 
        - string thumbnail
        - string youtubeId
        - array~array-key, string~ tags
        - Status status
        - int likes 
        - int comments 
        - int views
        - Live live
    }

    class Status {
        <<Enumeration>>
        Public
        Private
        Unlisted
    }

    class Live {
        - ?int id
        - DateTimeImmutable livedAt
        - Duration duration
        - string description 
        - Planning planning
    }

    class Duration {
        - int hours
        - int minutes
        - int seconds
        + DateTimeImmutable addTo(DateTimeImmutable date)
    }

    class Content {
        # ?int id
        # string title
        # string description
        # Collection~int, Live~ lives
        + string getName()*$ 
    }

    class VideoContent {
        <<Interface>>
        + string getVideoDescription()
    }

    class Kata {
        - string repository
        + string getName()$
        + string getVideoDescription()
    }

    class GettingStarted {
        - string repository
        + string getName()$
        + string getVideoDescription()
    }

    class Capsule {
        - string repository
        + string getName()$
        + string getVideoDescription()
    }

    class Project {
        - string repository
        + string getName()$
        + string getVideoDescription()
    }

    class Podcast {
        - array~string~ guests
        + string getName()$
        + string getVideoDescription()
    }

    class CodeReview {
        - string repository
        - string description
        + string getName()$
        + string getVideoDescription()
    }

    class Challenge {
        - Duration duraton
        - ?DateTimeImmutable startedAt
        - ?DateTimeImmutable endedAt
        - int basePoints
        - string repository
        - Collection~int, ChallengeRule~ rules
        + int getTotalPoints() 
        + boolean isSucceed() 
        + int getFinalPoints()
        + DateTimeImmutable getTheoreticalEndDate()
        + DateInterval getDiff()
        + string getName()$
        + string getVideoDescription()
    }

    class ChallengeRule {
        - ?int id
        - Challenge challenge
        - Rule rule
        - int hits
    }

    class Rule {
        - ?int id
        - string name
        - string description 
        - int points
    }

    class Planning {
        - ?int id
        - Collection~int, Live~ lives
        - DateTimeImmutable startedAt
        - DateTimeImmutable endedAt
        - string image
    }

    Content ..> VideoContent

    Challenge "1" --> "*" ChallengeRule

    ChallengeRule "*" --> "1" Rule

    Video "1" --> "1" Live

    Challenge "*" --> "1" Duration

    Live "*" --> "1" Duration

    Live --* Status

    Live "*" --> "1" Content

    Planning "1" <-- "*" Live

    Kata --|> Content

    Podcast --|> Content

    CodeReview --|> Content

    Project --|> Content

    GettingStarted --|> Content

    Capsule --|> Content

    Challenge --|> Content

```


## Contribuer
Veuillez prendre un moment pour lire le [guide sur la contribution](/CONTRIBUTING.md).

## Changelog
[CHANGELOG.md](/CHANGELOG.md) liste tous les changements effectués lors de chaque release.

## À propos
*twitch* a été conçu initialement par [Thomas Boileau](https://github.com/TBoileau). Si vous avez la moindre question, contactez [Thomas Boileau](mailto:t-boileau@email.com?subject=[Github]%20Twitch)