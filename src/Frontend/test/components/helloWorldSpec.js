import React from 'react';
import TestUtils from 'react/lib/ReactTestUtils';
import HelloWorld from '../../components/HelloWorld';
import chai from 'chai';

describe('helloWorld', () => {
  it('renders hello world', () => {
    const renderer = TestUtils.createRenderer();
    renderer.render(<HelloWorld />);

    const component = renderer.getRenderOutput();
    chai.expect(component.props.children[1].props.children).to.equal('Hello World!');
  });
});
