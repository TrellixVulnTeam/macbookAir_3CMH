from instapy import InstaPy

session = InstaPy(username='vdallcompany', password='thojose10vdall', headless_browser=True)
session.login()
session.like_by_tags(["roupas", "streetwear", "justapprove", "t-shirt", "shirts", "moda", "masculino", "masculina"], amount=5)
session.set_do_follow(True, percentage=50)
session.set_relationship_bounds(enabled=True, max_followers=8000)
session.end()

