#include <stdio.h>
#include <stdlib.h>
#include <string.h>


int main() {
	FILE* file;
	char filename[] = "a.txt";
	char buffer[0x100];
	int line_count = 0;

	file = fopen(filename, "r");
	if (file == NULL) {
		printf("파일을 열 수 없습니다.\n");
		return 1;
	}

	while (fgets(buffer, sizeof(buffer), file) != NULL) {
		line_count++;
	}

	rewind(file);

	char** lines = (char**)malloc(line_count * sizeof(char*));
	if (lines == NULL) {

		fclose(file);
		return 1;
	}

	for (int i = 0; i < line_count; i++) {
		fgets(buffer, sizeof(buffer), file);

		int len = strlen(buffer);
		lines[i] = (char*)malloc(len * sizeof(char));
		if (lines[i] == NULL) {
			
			fclose(file);
			for (int j = 0; j < i; j++) {
				free(lines[j]);
			}
			free(lines);
			return 1;
		}

		strcpy(lines[i], buffer);
	}
	
	fclose(file);

	for (int i = 0; i < line_count; i++) {
		printf("%s", lines[i]);
		free(lines[i]);
	}
	printf("\n");

	free(lines);

	return 0;
}
