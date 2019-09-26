def linear_search(integers, value):

    for integer in integers:

        if integer == value:
            return True

    return False

integers = [3,5,7,11,13,17,21,23,29,31]

found_or_not = binary_search(integers,9000)

if found_or_not:
    print("In the List")
else:
    print("Not in the list")

print("I added this statement as a test")
