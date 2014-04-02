# Quite Simple Publishing System

This is a single-author email-based content management system. It's as simple as
it can be.

Status: **pre-alpha**

## Motivation

Sometimes you just want to get your content live, and that's it. No bulky
installs, no themes or plugins to choose from. As simple as it can be.

I also didn't want to have to login in order to post something. So you have to
sign your content instead. QSPS will check for a valid sgnature and act
accordingly.


So if you have an email address, a PGP key and a desire for simplicity, go ahead
and give this one a try.

## Features

* Email-based
* Plain-file index (no database)
* Single-user (currently)

**What QSPS is:** The simplest, most down-to-earth blog engine you'll find, with
a special appeal to crypto-geeks. In essence, QSPS is an application of the
PGP Web of Trust to the publishing world.

**What QSPS is not:** A Wordpress/Joomla/etc. replacement. It's not a _content
management system_. it won't give you a pretty editor or a login panel, or a
fancy administration page.

## Decisions

(todo: migrate this section to a wiki)

### OpenSSL vs GnuPG

* OpenSSL has a complicated interface (compared to GnuPG) but is more "correct"
* GnuPG is much easier to work with while providing the same functionality
* GnuPG works with the local keyring and requires a `setenv` command to get
around this

The winner is **OpenSSL**.

### `reply-to` field: Should it result in a comment?

It's very hard to set the reply-to header field without actually replying to an
email. Thus comments can be sent with `Re: <mail-ID>`, in the subject line,
where the `mail-ID` that created each node has been made  public. This has the
side effect of identifying each comment to the node revision.
