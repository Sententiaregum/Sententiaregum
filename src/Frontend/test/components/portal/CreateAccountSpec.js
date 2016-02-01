/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import CreateAccount from '../../../components/portal/CreateAccount';
import TestUtils from 'react/lib/ReactTestUtils';
import React from 'react';
import chai from 'chai';

describe('CreateAccount', () => {
  it('renders registration page', () => {
    const renderer = TestUtils.createRenderer();
    renderer.render(<CreateAccount />);
    const output = renderer.getRenderOutput();

    chai.expect(output.props.children[0].props.children.props.content).to.equal('pages.portal.head');
    chai.expect(output.props.children[1].props.children[0].type.name).to.equal('InfoBox');
    chai.expect(output.props.children[1].props.children[1].type.name).to.equal('Form');
  });
});
