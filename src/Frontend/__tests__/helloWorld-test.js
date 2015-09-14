jest.dontMock('../components/helloWorld.js');

describe('This should', function() {
    it('display helloWorld to the screen', function() {
        expect('Hello World!').toBe('Hello World!');
    });
});
