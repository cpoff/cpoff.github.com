+++
author = "Curt Poff"
categories = ["dev"]
date = "2019-01-14T19:53:00-07:00"
description = "As mentioned earlier, I'm now running a Hugo static site generator. It's going so well that I decided to install the Forestry.io CMS as phase 2."
layout = "post"
tags = ["curtpoff.com", "forestry.io", "hugo"]
thumbnail = "/uploads/Screenshot 2019-01-14 20.38.10.png"
title = "Forestry.io now behind the scenes"

+++
As [mentioned earlier](/posts/new-hugo-site/ "Hugo now running this site"), I'm now running a Hugo static site generator. It's going great so far so I decided to install a CMS as phase 2.

<!--more-->

The open-source CMS I'm running is built by [Forestry](https://forestry.io "Forestry.io"). And it only took a little tinkering to get the template configuration solved.

Site import and config was easy. But I spent an hour or so trying to build a new Post template from scratch (which I almost got completed) before I came across a paragraph in their docs detailing how I could find an existing post and create a new template from it.

So boom! I found my old starter template with detailed field names, filled in the default values, did a couple slight tweaks. Now that Post template is available as a starter for all posts going forward - including this one - with the push of a button.

Turns out all I had to do was [RTFM](https://forestry.io/docs/quickstart/configure-cms/).