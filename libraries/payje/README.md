# Payje PHP Library for WordPress

## Installing the library

#### Step 1. Add the Repository as a Remote

```
git remote add -f subtree-payje git@bitbucket.org:yiedpozi/payje.git
```

#### Step 2. Add the Repo as a Subtree
```
git subtree add --prefix libraries/payje subtree-payje master --squash
```

#### Step 3. Update the Subtree
```
git fetch subtree-payje master
git subtree pull --prefix libraries/payje subtree-payje master --squash
```