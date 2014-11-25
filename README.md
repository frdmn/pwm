pwm (Packal Workflow Manager)
=============================

(Hacky) command line tool to manage and maintain your Alfred/[Packal](http://packal.org) workflows as well as exploring new ones or updating them.

__Caution:__ Since Packal doesn't provide a API, I had to implement a scraper/caching system to store _all_ informations about any workflow available on Packal locally in a JSON file. Not the best-practise but I have no clue what to do to improve this method.

![](http://up.frd.mn/De577.png)

## Usage

Show available commands:

`pwm` 

---

Update the workflow cache directly from packal.org:

`pwm cache`

---

List currently installed workflows:

`pwm list` 

---

Search for a specific workflow:

`pwm search <term>` 

## Installation

1. Clone repository:  
  `cd /usr/local/src`
  `git clone https://github.com/frdmn/ssltools.git`
1. Symlink into `$PATH`:  
  `ln -s /usr/local/src/pwm/pwm /usr/bin/`
1. Update the cache at least once before executing any commands:  
  `pwm cache`

## Version

0.1.0

## License

[WTFPL](LICENSE)
