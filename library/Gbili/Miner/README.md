## Optional capturing groups
E.g. the group is followed by a question mark, or the group is inside an optional block etc.

### Problem
Such groups are a pain in the ass, because they will change the overall group count. If the _optional group_ is not the last capturing group, all the other group numbers may be increased or decreased depending on the _optional group_, if matches something or not. We'll call this fenomenon: _group number shifting_.

### Stinky Solutions
#### 1. Split regex
The first solution, is to put that optional capturing group at the end of the regex. The __drawback__ is that the regex may need to be truncated into two actions because of this. And you would put the _optional group_ at the end of the former's regex.

#### 2. Move to another action
Another solution is to make that _optional group_ __not optional__, if possible...
Let's consider this _optional group_ inside this regex : `#not optional( my optional text)? not optional txt#uis`.
The conversion goes like this: you should widen the optional group parenthesis to _non optional text_ and mark the original _optional group_ as __non capturing__: `#(not optional(?: my optional text)? not optional txt)#uis`. Thus the enlarged _optional group_ becomes _non optional_ and the original _optional group_ is not captured and still optional. This will avoid group number conflicts.

You may be thinking that now you will not be able to capture the _optional group_ if it matches something. An you are right; not in the current action.
 
You will need to remove the `$action->spitGroupAsEntity($optionalGroupNumber, $entityIdentifier)` and move it to a new child _extract action_. Inside the child _extract action_ you will call `$childAction->setInputParentRegexGroup($optionalGroupNumber)`, where `$optionalGroupNumber` is not optional anymore in the parent action (because of what we just did). 
You will then add the regex that you had in the parent action before any modification was made, and you will try to match that _optional group_ from what the parent returns.
Important: the child action has to be optional so you should call : `$childAction->setAsOptional()`. This will prevent the engine from crashing if no matches are found.

### Better solution, named groups
Use _named capturing groups_: in PCRE _named group_'s syntax looks like this `(?P<mygroup>some text)`. You can then pass that _group name_ to `$action->spitGroupAsEntity('mygroup', $entityIdentifier)`. If you use this solution you would have to name all your groups, since named groups are also present in the `preg_match($matches)`'s `$matches` array as numbered groups. So an optional group would also cause _group number shifting_.


# List of events

__Application/Application__:
_manageExecutedAction.hasFinalResults_ : recieves results as params, usefull to save results
_executeAction.pre_ : before action is executed
_executeAction.success_ : after action executed and execution succeed
_executeAction.fail_ : after exectution fail. Here execution fail is not necesarily fatal, since actions can be optional or normal. To monitor the fatal scenario, listen to _manageFail.normalAction_
_executeAction.post_ : after execution whether execution fails or succeeds.

