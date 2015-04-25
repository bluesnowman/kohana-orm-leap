###
 # Copyright © 2011–2015 Spadefoot Team.
 #
 # Unless otherwise noted, Leap is licensed under the Apache License,
 # Version 2.0 (the "License"); you may not use this file except in
 # compliance with the License. You may obtain a copy of the License
 # at:
 #
 #      http://www.apache.org/licenses/LICENSE-2.0
 #
 # Unless required by applicable law or agreed to in writing, software
 # distributed under the License is distributed on an "AS IS" BASIS,
 # WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 # See the License for the specific language governing permissions and
 # limitations under the License.
###

########################################################################
# This Makefile is for running PHPUnit tests.
########################################################################

########################################################################
# Macro Definitions
########################################################################

BOOTSTRAP_FILE = ./Bootstrap.php
BOOTSTRAP_SWITCH = --bootstrap $(BOOTSTRAP_FILE)

ARCHIVE_FILE = phpunit.phar
DOWNLOAD_URL = https://phar.phpunit.de/$(ARCHIVE_FILE)
OPERATING_SYSTEM := $(shell uname)
ifeq ($(OPERATING_SYSTEM), Darwin)
	DOWNLOAD_COMMAND = curl -O $(DOWNLOAD_URL)
else
	DOWNLOAD_COMMAND = wget $(DOWNLOAD_URL)
endif

PROGRAM_NAME = phpunit

UNIT_TESTS = .

########################################################################
# Rules (for Running)
########################################################################

# make execute GROUP=thegroupname
execute:
ifndef GROUP
	./phpunit $(BOOTSTRAP_SWITCH) $(UNIT_TESTS)
else
	./phpunit $(BOOTSTRAP_SWITCH) --group $(GROUP) $(UNIT_TESTS)
endif

########################################################################
# Rules (for Installing)
########################################################################

# make install
install:
	$(DOWNLOAD_COMMAND)
	chmod +x $(ARCHIVE_FILE)
	mv $(ARCHIVE_FILE) $(PROGRAM_NAME)
	./$(PROGRAM_NAME) --version

# make uninstall
uninstall:
	rm -f $(PROGRAM_NAME)
