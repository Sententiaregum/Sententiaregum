
import React from 'react';
import TestUtils from 'react/lib/ReactTestUtils.js'
import HelloWorld from '../../components/helloWorld.js'

describe('helloWorld', () => {
    it('renders hello world', () => {
        const renderer = TestUtils.createRenderer();
        renderer.render(<HelloWorld />);

        const component = renderer.getRenderOutput();
        expect(component._store.props.children).toBe('Hello World!');
    });
});
