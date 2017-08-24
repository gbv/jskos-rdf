info:
	@echo "Usage: make test|doc|install"

# pass any target to composer
$(MAKECMDGOALS):
	composer $(MAKECMDGOALS)
